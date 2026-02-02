<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                الصفحة الرئيسية
            </h2>
            <h2 class="text-blue-500 hover:text-blue-600"><a href="/preferences">تفضيلاتي</a></h2>
        </div>

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('books.search') }}" method="GET">
                        <div class="flex justify-center gap-5">
                            <input type="text" name="query" placeholder="ابحث عن الكتاب الذي تريده" class="border border-gray-300 rounded-lg px-4 py-2 w-96 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <button class="bg-blue-500 text-white w-32 px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors" type="submit">بحث</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>