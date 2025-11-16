<div data-aos="fade-down" class="w-full bg-slate-400 dark:bg-slate-500 overflow-hidden">
    @php
        $section = App\Models\Section::where("name", "titleHeader")->first();
    @endphp

    @if ($section && $section->content)
        <a href="{{ route('home') }}" class="block w-full">
            <img
                src="{{ asset($section->content) }}"
                alt="Title Header"
                class="w-screen max-w-none h-auto object-cover block"
            >
        </a>
    @endif
</div>
<header data-aos="fade-down" id="header" class=" max-md:flex md:px-4 max-md:px-2 py-3 z-50 bg-blue-500 dark:bg-teal-700 dark:backdrop-blur-lg dark:bg-opacity-50 flex justify-between items-center text-gray-800 dark:text-gray-100 transition-all duration-300 shadow-md">
    @auth
        <div class="relative">
            <button id="menu_button" aria-haspopup="true" aria-expanded="false" class="rounded-lg dark:bg-teal-600 bg-white text-gray-800 dark:text-gray-100 py-2 max-md:py-1 max-md:px-3 px-6 transition-all">
                منو
            </button>
            <div id="menu_section" class="absolute right-0 opacity-0 mt-2 w-48 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg transition-opacity duration-300 invisible">
                    <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">پروفایل</a>
                @role("super admin")
                    <a href="{{ route('admin.panel') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">تغییر صفحه اصلی و ابزار ها</a>
                    <a href="{{ route('admin.index_users') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">کاربران</a>
                @endrole
                @role('admin|super admin')
                    <a href="{{ route('admin.index_contacts') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">فرم های تماس</a>
                    <a href="{{ route('writer.magazine.create') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">نوشتن نشریه</a>
                    <a href="{{ route('writer.new.create') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">نوشتن خبر</a>
                    <a href="{{ route('writer.event.create') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">نوشتن رویداد</a>
                    <a href="{{ route('admin.index_comments') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">کامنت‌ها</a>
                    <a href="{{ route('admin.index_contents') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">مدیریت محتوا ها</a>
                @endrole

                @role('user')
                    <a href="{{ route('user.create') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">ارسال مقاله</a>
                @endrole

                <a href="{{ route('auth.logout') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">خروج از حساب</a>
            </div>
        </div>
    @else
        <a href="{{ route('login') }}" class="rounded-lg py-2 px-4  max-md:py-1 max-md:px-3 bg-green-500 text-white hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-600 transition-all">ورود</a>
    @endauth

    <nav class="md:flex gap-3 max-lg:gap-1 items-center sm:text-lg text-sm">
        <a href="{{ route('news') }}" class="hover:bg-blue-100 dark:hover:bg-blue-800 px-3 py-2 rounded-md transition-all">اخبار</a>
        <a href="{{ route('magazines') }}" class="hover:bg-blue-100 dark:hover:bg-blue-800 px-3 py-2 rounded-md transition-all">نشریه‌ها</a>
        <a href="{{ route('events') }}" class="hover:bg-blue-100 dark:hover:bg-blue-800 px-3 py-2 rounded-md transition-all">رویدادها</a>
        <a href="{{ route('contact') }}" class="hover:bg-blue-100 dark:hover:bg-blue-800 px-3 py-2 rounded-md transition-all max-sm:hidden">تماس با ما</a>
    </nav>

    <form method="GET" class="hidden lg:flex items-center" action="{{ route('search') }}">
        <input name="search" type="text" value="{{ $_GET['search'] ?? '' }}" class="rounded-r-lg px-3 py-2 w-64 focus:outline-none dark:bg-gray-700 dark:text-gray-200 bg-gray-200 text-gray-700 focus:ring-2 ring-blue-400 transition-all">
        <input type="hidden" value="all" name="type">
        <button type="submit" class="ml-2 shadow-lg rounded-l-lg bg-blue-500 text-white px-4 py-2 hover:bg-blue-600 transition-all">
            جستجو
        </button>
    </form>
    <button id="btn_toggle_drkmd" class="rounded-lg dark:bg-teal-600 bg-white text-gray-800 dark:text-gray-100 py-2 max-md:py-1 max-md:px-3 px-6 transition-all hover:bg-gray-200 dark:hover:bg-teal-500">
        <span id="dark-mode-icon" class="material-icons">تاریک</span>
    </button>
</header>
