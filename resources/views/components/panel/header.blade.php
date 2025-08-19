<header data-aos="fade-down"
    class=" w-full px-3 z-50 bg-green-600 dark:bg-slate-400 rounded-b-xl dark:backdrop-blur-lg dark:bg-opacity-40 flex justify-between text-black dark:text-white">
    @auth
        <div class="relative md:mt-0 ">
            <button id="menu_button"
                class=" rounded-xl dark:bg-slate-500 my-3 h-12 w-28 bg-white transition-all hover:bg-slate-400 hover:text-white">منو</button>

            <div id="menu_section"
                class="absolute right-0 opacity-0 mt-2 w-48 bg-slate-300 dark:bg-slate-700 rounded-xl shadow-lg transition-opacity duration-300 invisible">
                <a href="{{ route('user.profile') }}"
                    class="block px-4 py-2 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors duration-300">ناحیه
                    کاربری</a>

                @role('writer|admin|super admin')
                    <!-- CHANGED: Update to existing route name for magazine create -->
                    <a href="{{ route('writer.magazine.create') }}"
                        class="block px-4 py-2 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors duration-300">نوشتن
                        نشریه جدید</a>
                    <a href="{{ route('news') }}"
                        class="block px-4 py-2 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors duration-300">نوشتن
                        اخبار جدید
                    </a>
                @endrole
                @role('user')
                    <a href="{{ route('contact') }}"
                        class="block px-4 py-2 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors duration-300">تماس
                        با مدیر</a>
                    <a href="{{ route('user.create') }}"
                        class="block px-4 py-2 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors duration-300">نوشتن
                        پیشنهاد</a>
                @endrole
                <a href="{{ route('auth.logout') }}"
                    class="block px-4 py-2 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors duration-300">خروج
                    از حساب کاربری
                </a>
                <a href="{{ route('search') }}"
                    class="block px-4 py-2 md:hidden hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors duration-300">
                    جست و جو
                </a>
            </div>
        </div>
    @else
        <a href="{{ route('login') }}"
            class=" rounded-xl my-3 h-12 dark:bg-slate-500 w-28 bg-white transition-all hover:bg-slate-500 flex flex-col text-center justify-center hover:text-white">ورود</a>
    @endauth
    <div class=" h-full flex justify-center text-center">
        <a href="{{ route('contact') }}"
            class=" hover:bg-red-600 max-sm:hidden transition-all text-nowrap px-2 py-6 min-w-20 h-full">تماس با ما</a>
        <!-- CHANGED: Remove link to non-existing report route -->
        <a href="{{ route('news') }}"
            class=" hover:bg-red-600 max-sm:hidden transition-all text-nowrap px-2 py-6 min-w-20 h-full">اخبار</a>
        <!-- CHANGED: Fix label (list page is magazines) -->
        <a href="{{ route('magazines') }}"
            class=" hover:bg-red-600 max-sm:hidden transition-all text-nowrap px-2 py-6 min-w-20 h-full">نشریه ها</a>
        <a href="{{ route('events') }}"
            class=" hover:bg-red-600 max-sm:hidden transition-all text-nowrap px-2 py-6 min-w-20 h-full">رویداد ها</a>
        <a href="{{ route('home') }}"
            class=" hover:bg-red-600 sm:hidden transition-all text-nowrap px-2 py-6 min-w-20 h-full">صفحه اصلی</a>

    </div>
    <form method="GET" class=" flex gap-2 max-lg:hidden" action="{{ route('search') }}">
        <input name="search" type="text" value="{{ $_GET['search'] ?? '' }}"
            class=" rounded-xl hover:ring-2 ring-blue-500 focus:ring-4 transition-all h-8 px-2 focus:outline-none  my-auto">
        <button
            class=" rounded-xl h-8 my-auto px-2 text-white bg-blue-500 hover:bg-blue-400 focus:bg-blue-600 transition-all">جست
            و جو</button>
    </form>
    <button id="btn_toggle_drkmd"
        class=" rounded-xl dark:bg-slate-500 my-3 h-12 w-28 bg-white transition-all hover:bg-slate-400 hover:text-white">حالت
        شب</button>
</header>
