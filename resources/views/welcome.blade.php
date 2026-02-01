<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وكيل المكتبة الذكي - نظام مكتبة رقمية مدعوم بالذكاء الاصطناعي</title>
    <!-- تضمين Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- أيقونات Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* تخصيصات إضافية للتنسيق العربي */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .step-card {
            transition: transform 0.3s ease;
        }
        .step-card:hover {
            transform: translateY(-5px);
        }
        .feature-card {
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    
    <!-- قسم الهيرو الرئيسي -->
    <header class="bg-gradient-to-r from-blue-900 to-indigo-800 text-white">
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto text-center">
                <!-- العنوان الرئيسي -->
                <h1 class="text-4xl md:text-5xl font-bold mb-4">وكيل المكتبة الذكي المدعوم بالذكاء الاصطناعي</h1>
                
                <!-- الوصف المختصر -->
                <p class="text-xl md:text-2xl mb-8 text-blue-100">
                    نظام توصيات كتب قصصية ذكي يتعلم من تفضيلاتك ليقدم لك اقتراحات شخصية تناسب ذوقك الأدبي
                </p>
                
                <!-- أزرار الحث على الإجراء -->
                <div class="flex flex-col sm:flex-row justify-center gap-4 mt-10">
                    <a href="#" class="bg-white text-blue-900 font-bold py-3 px-8 rounded-lg text-lg hover:bg-blue-50 transition duration-300 shadow-lg">
                        <i class="fas fa-robot mr-2"></i> احصل على توصيات شخصية
                    </a>
                    <a href="#" class="bg-transparent border-2 border-white text-white font-bold py-3 px-8 rounded-lg text-lg hover:bg-white hover:text-blue-900 transition duration-300">
                        <i class="fas fa-book-open mr-2"></i> تصفح الكتب القصصية
                    </a>
                </div>
                
                <!-- رسم توضيحي بسيط -->
                <div class="mt-16 text-blue-200">
                    <i class="fas fa-brain text-6xl mb-4"></i>
                    <p class="text-lg">نظام ذكي يوصي بالكتب بناءً على تحليل تفضيلات القراءة الخاصة بك</p>
                </div>
            </div>
        </div>
    </header>

    <!-- قسم كيف يعمل وكيل الذكاء الاصطناعي -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">كيف يعمل وكيل الذكاء الاصطناعي؟</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">عملية بسيطة من ثلاث خطوات لتقديم أفضل توصيات الكتب القصصية</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- الخطوة الأولى -->
                <div class="step-card bg-blue-50 p-8 rounded-xl border border-blue-100">
                    <div class="text-center mb-6">
                        <div class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                        <i class="fas fa-user-edit text-blue-600 text-4xl mb-4"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">قدم تفضيلاتك</h3>
                    <p class="text-gray-700 text-lg text-center">
                        اختر أنواع الكتب التي تحبها، مستوى الصعوبة المناسب، الموضوعات المفضلة وغيرها من المعايير
                    </p>
                </div>
                
                <!-- الخطوة الثانية -->
                <div class="step-card bg-blue-50 p-8 rounded-xl border border-blue-100">
                    <div class="text-center mb-6">
                        <div class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                        <i class="fas fa-brain text-blue-600 text-4xl mb-4"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">يحلل الذكاء الاصطناعي اهتماماتك</h3>
                    <p class="text-gray-700 text-lg text-center">
                        يقوم نظام الذكاء الاصطناعي بتحليل تفضيلاتك ومقارنتها مع آلاف الكتب لتحديد الأنسب لك
                    </p>
                </div>
                
                <!-- الخطوة الثالثة -->
                <div class="step-card bg-blue-50 p-8 rounded-xl border border-blue-100">
                    <div class="text-center mb-6">
                        <div class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                        <i class="fas fa-book text-blue-600 text-4xl mb-4"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">يتلقى توصيات كتب مناسبة</h3>
                    <p class="text-gray-700 text-lg text-center">
                        تحصل على قائمة شخصية بكتب قصصية تناسب ذوقك تمامًا، مع تفاصيل كل كتاب وتوافره
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الميزات الرئيسية -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">ميزات النظام الرئيسية</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">نظام متكامل يجمع بين الذكاء الاصطناعي والمكتبة الرقمية</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8 max-w-6xl mx-auto">
                <!-- ميزة 1 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md">
                    <div class="flex items-start mb-6">
                        <div class="bg-blue-100 p-4 rounded-lg mr-4">
                            <i class="fas fa-user-check text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">توصيات شخصية بالذكاء الاصطناعي</h3>
                            <p class="text-gray-700">يحلل النظام تفضيلاتك القرائية ويقدم اقتراحات كتب تناسب ذوقك الشخصي بدقة عالية</p>
                        </div>
                    </div>
                </div>
                
                <!-- ميزة 2 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md">
                    <div class="flex items-start mb-6">
                        <div class="bg-blue-100 p-4 rounded-lg mr-4">
                            <i class="fas fa-filter text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">تصفية ذكية متقدمة</h3>
                            <p class="text-gray-700">إمكانية التصفية حسب النوع، مستوى الصعوبة، الموضوع، السعر والمزيد من المعايير</p>
                        </div>
                    </div>
                </div>
                
                <!-- ميزة 3 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md">
                    <div class="flex items-start mb-6">
                        <div class="bg-blue-100 p-4 rounded-lg mr-4">
                            <i class="fas fa-database text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">مكتبات رقمية متكاملة</h3>
                            <p class="text-gray-700">وصول إلى آلاف الكتب القصصية من مصادر رقمية متنوعة في مكان واحد</p>
                        </div>
                    </div>
                </div>
                
                <!-- ميزة 4 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md">
                    <div class="flex items-start mb-6">
                        <div class="bg-blue-100 p-4 rounded-lg mr-4">
                            <i class="fas fa-search text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">بحث ذكي وسريع</h3>
                            <p class="text-gray-700">تقنية بحث متقدمة تمكنك من العثور على الكتاب المناسب بسرعة باستخدام مصطلحات بسيطة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم إجراءات المستخدم -->
    <section class="py-16 bg-gradient-to-r from-blue-800 to-indigo-800 text-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">ابدأ رحلتك مع وكيل المكتبة الذكي</h2>
                <p class="text-xl mb-10 text-blue-100">
                    سجل حسابًا الآن للاستفادة من الميزات الكاملة والتوصيات الشخصية التي تتحسن كلما استخدمت النظام أكثر
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-6 mb-12">
                    <a href="{{ route('register') }}" class="bg-white text-blue-900 font-bold py-4 px-10 rounded-lg text-xl hover:bg-blue-50 transition duration-300 shadow-lg">
                        <i class="fas fa-user-plus mr-3"></i> إنشاء حساب جديد
                    </a>
                    <a href="{{ route('login') }}" class="bg-transparent border-2 border-white text-white font-bold py-4 px-10 rounded-lg text-xl hover:bg-white hover:text-blue-900 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-3"></i> تسجيل الدخول
                    </a>
                </div>
                
                <!-- فوائد الحساب -->
                <div class="bg-blue-900/50 p-8 rounded-xl text-right">
                    <h3 class="text-2xl font-bold mb-6">مزايا إنشاء حساب في النظام:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 ml-3 text-xl"></i>
                            <span>توصيات كتب شخصية تتحسن مع الوقت</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 ml-3 text-xl"></i>
                            <span>حفظ سجل الكتب المقروءة والمفضلة</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 ml-3 text-xl"></i>
                            <span>متابعة توصيات جديدة أسبوعيًا</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 ml-3 text-xl"></i>
                            <span>مشاركة آرائك وتقييماتك للكتب</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- الفوتر -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-8 md:mb-0 text-center md:text-right">
                    <h2 class="text-2xl font-bold mb-2">وكيل المكتبة الذكي المدعوم بالذكاء الاصطناعي</h2>
                    <p class="text-gray-400">نظام توصيات كتب قصصية ذكي - نموذج أكاديمي</p>
                </div>
                
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-university text-3xl text-blue-400"></i>
                    </div>
                    <p class="text-gray-400">مشروع تخرج / بحث أكاديمي</p>
                    <p class="text-sm text-gray-500 mt-2">هذا النموذج لأغراض بحثية وتعليمية فقط وليس منتجًا تجاريًا</p>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-500">
                <p>جميع الحقوق محفوظة &copy; <span id="currentYear"></span> - مشروع مكتبة ذكية مدعومة بالذكاء الاصطناعي</p>
            </div>
        </div>
    </footer>

    <!-- سكريبت بسيط -->
    <script>
        // تعيين السنة الحالية في الفوتر
        document.getElementById('currentYear').textContent = new Date().getFullYear();
        
        // تأثير بسيط عند التمرير (لأغراض العرض فقط)
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // تطبيق التأثير على العناصر
            const animatedElements = document.querySelectorAll('.step-card, .feature-card');
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>