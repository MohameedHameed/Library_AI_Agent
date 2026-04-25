<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * System instructions for the Gemini chatbot.
     * The bot ONLY helps with book recommendations in Arabic.
     */
    private function getSystemInstruction(): string
    {
        return <<<'PROMPT'
أنت مساعد ذكي متخصص حصرياً في توصيات الكتب ومساعدة المستخدمين في اختيار الكتب المناسبة لهم.

## القواعد الصارمة:
1. يجب أن تكون جميع إجاباتك باللغة العربية فقط.
2. أنت متخصص فقط في مجال الكتب والقراءة. لا تجب على أي سؤال خارج هذا النطاق.
3. إذا سألك المستخدم عن أي موضوع لا يتعلق بالكتب أو القراءة أو الأدب، يجب أن ترفض بلطف وتقول: "عذراً، أنا متخصص فقط في توصيات الكتب ومساعدتك في اختيار الكتب المناسبة. هل يمكنني مساعدتك في اختيار كتاب؟"
4. لا تقدم معلومات طبية أو قانونية أو مالية أو سياسية أو أي معلومات خارج نطاق الكتب.
5. لا تكتب أكواد برمجية أو تساعد في مهام برمجية.

## ما يمكنك فعله:
- اقتراح كتب بناءً على اهتمامات المستخدم وتفضيلاته.
- تقديم ملخصات قصيرة عن الكتب.
- مقارنة الكتب ومساعدة المستخدم في الاختيار بينها.
- اقتراح كتب مشابهة لكتب أحبها المستخدم.
- تقديم معلومات عن المؤلفين وأعمالهم.
- اقتراح كتب حسب النوع الأدبي (رواية، علمي، تاريخي، تطوير ذاتي، إلخ).
- مساعدة المستخدم في وضع خطة قراءة.
- الإجابة على أسئلة حول عالم الكتب والنشر والأدب.

## أسلوب الرد:
- كن ودوداً ومتحمساً للكتب.
- استخدم لغة عربية فصحى سهلة ومفهومة.
- قدم إجابات منظمة ومفيدة.
- عند اقتراح كتب، اذكر اسم الكتاب والمؤلف ونبذة قصيرة عن كل كتاب.
- استخدم الرموز التعبيرية بشكل معتدل لجعل المحادثة ممتعة (📚، ⭐، 📖، ✨).
PROMPT;
    }

    /**
     * Send a message to the Gemini API and return the response.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $userMessage = $request->input('message');
        $history = $request->input('history', []);

        $apiKey = trim(config('services.gemini.api_key'));

        if (!$apiKey) {
            return response()->json([
                'error' => 'مفتاح API غير مُعد. يرجى التواصل مع المسؤول.',
            ], 500);
        }

        try {
            // Build the contents array with conversation history
            $contents = [];

            // Add conversation history
            foreach ($history as $msg) {
                $contents[] = [
                    'role' => $msg['role'],
                    'parts' => [['text' => $msg['text']]],
                ];
            }

            // Add current user message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]],
            ];

            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $apiKey,
            ])->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent",
                [
                    'systemInstruction' => [
                        'parts' => [['text' => $this->getSystemInstruction()]],
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topP' => 0.95,
                        'topK' => 40,
                        'maxOutputTokens' => 1024,
                    ],
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'عذراً، لم أتمكن من معالجة طلبك. حاول مرة أخرى.';

                return response()->json([
                    'reply' => $reply,
                ]);
            }

            // Handle Specific Error Codes
            if ($response->status() === 429) {
                return response()->json([
                    'error' => 'أنا مشغول قليلاً الآن بسبب كثرة الطلبات. يرجى المحاولة مرة أخرى بعد دقية. ⏳',
                ], 429);
            }

            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'error' => 'حدث خطأ أثناء الاتصال بالمساعد الذكي. حاول مرة أخرى.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());

            return response()->json([
                'error' => 'حدث خطأ غير متوقع. حاول مرة أخرى لاحقاً.',
            ], 500);
        }
    }
}
