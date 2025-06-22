<?php

namespace Database\Seeders;

use App\Models\Scope;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class ProdSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where("username", "admin")->first();

        if (!$admin) {
            throw new \Exception('Admin user not found.');
        }

        // Insert categories
        DB::table("categories")->insert([
            ["name" => "توسعه فردی"],
            ["name" => "ایرانی - اسلامی"],
            ["name" => "سیاسی"],
            ["name" => "درس"],
            ["name" => "اخلاقی"],
            ["name" => "فناوری"],
            ["name" => "عرفان"],
        ]);

        // Insert scopes
        DB::table("scopes")->insert([
            ["name" => "طلاب سطح سه"],
            ["name" => "تمامی طلاب"],
            ["name" => "تمامی اقشار"],
            ["name" => "اساتید"],
            ["name" => "فرهنگیان"],
            ["name" => "مدیران"],
            ["name" => "بسیجیان"],
        ]);

        $tolab_3 = Scope::where("name", "طلاب سطح سه")->first();
        if (!$tolab_3) {
            Log::warning('Scope "طلاب سطح سه" not found.');
        }

        // Insert magazines
        DB::table('magazines')->insert([
            [
                "slug" => Str::slug("در مسیر تمدن ۱"),
                "title" => "در مسیر تمدن ۱",
                "image" => "images/0IzNfotL9DzsxR3hjvI4LCEs5uZ1mjnVltxDhdaq.jpg",
                "pdf" => "3biDSvfoezPAntzT40lLTyKOz7UPxCNIKufA4Kps.pdf",
                "user_id" => $admin->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
        // Insert khabars
        DB::table('khabars')->insert([
            [
                "slug" => Str::slug("تقویم آموزشی 1404-1403 ویرایش 1"),
                "pdf" => "attachments/2JanTYQ4oaHFgR2J6LH8XuAFlGSWcTi6cwEnArDQ.pdf",
                'title' => "تقویم آموزشی 1404-1403 ویرایش 1",
                'body' => "متن کامل تقویم آموزشی...",
                'image' => 'images/ONkNhwJUAsLgkjU4i0ND6JPS7ZFXB7ZveE3WX1Ns.png',
                'user_id' => $admin->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'scope_id' => $tolab_3 ? $tolab_3->id : null,
            ],
            [
                'title' => 'لیست نهایی دروس اصلی اختبار',
                "slug" => Str::slug('لیست نهایی دروس اصلی اختبار'),
                "pdf" => "attachments/2JanTYQ4oaHFgR2J6LH8XuAFlGSWcTi6cwEnArDQ.pdf",
                'body' => 'متن کامل لیست دروس...',
                'image' => 'images/eXZWATxSMlgPAPFVG1RG2kvbfxpawEjaWgBokOwB.png',
                'user_id' => $admin->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'scope_id' => $tolab_3 ? $tolab_3->id : null,
            ],
            [
                'title' => 'درس اخلاق یازدهم مهر',
                "slug" => Str::slug('درس اخلاق یازدهم مهر'),
                "pdf" => "attachments/2JanTYQ4oaHFgR2J6LH8XuAFlGSWcTi6cwEnArDQ.pdf",
                'body' => 'متن کامل جلسه اخلاق...',
                'image' => 'images/dqwHIw4JBaxW0hkWXnhFCqBV5jMLxockoBX2QLDz.jpg',
                'user_id' => $admin->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'scope_id' => $tolab_3 ? $tolab_3->id : null,
            ],
            [
                "slug" => Str::slug('شهادت مجاهد سید حسن نصرالله'),
                "pdf" => "attachments/2JanTYQ4oaHFgR2J6LH8XuAFlGSWcTi6cwEnArDQ.pdf",
                'title' => 'شهادت مجاهد سید حسن نصرالله',
                'body' => 'متن کامل پیام شهادت...',
                'image' => 'images/UHg8gz9G89zshFCOnI5XnWhbiYPsU98B2eKK7xkj.jpg',
                'scope_id' => $tolab_3 ? $tolab_3->id : null,
                'user_id' => $admin->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                "slug" => Str::slug('افتتاحیه سال تحصیلی ۱۴۰۳ - ۱۴۰۴'),
                "pdf" => "attachments/2JanTYQ4oaHFgR2J6LH8XuAFlGSWcTi6cwEnArDQ.pdf",
                'title' => 'افتتاحیه سال تحصیلی ۱۴۰۳ - ۱۴۰۴',
                'body' => 'متن کامل افتتاحیه...',
                'image' => 'images/hvMWHWXWG1wb1aohbAMFHP58RrS5S74izzUvLFdf.jpg',
                'scope_id' => $tolab_3 ? $tolab_3->id : null,
                'user_id' => $admin->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
