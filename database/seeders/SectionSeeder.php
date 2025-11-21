<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            'titleHeader' => [
                'content' => 'images/titleHeader.jpg',
            ],
            'titleFooter' => [
                'content' => 'images/titleFooter.jpg',
            ],
            'defaultContentImage' => [
                'content' => 'images/defaultContentImage.jpg',
            ],
            'aboutUs' => [
                'content' => 'موسسه ما با هدف ارائه خدمات آموزشی، پژوهشی و فرهنگی به طلاب و علاقه‌مندان به علوم اسلامی تأسیس شده است. با تیمی مجرب و متخصص، تلاش می‌کنیم تا بستری مناسب برای یادگیری و تبادل نظر فراهم کنیم.',
            ],
            'contactUs' => [
                'content' => 'برای ارتباط با ما می‌توانید از طریق فرم تماس، شماره تلفن‌های موجود و یا آدرس ایمیل درج‌شده در این بخش اقدام کنید. همچنین دفتر موسسه در ساعات اداری آماده پاسخ‌گویی به شما عزیزان است.',
            ],
            'magazineGuide' => [
                'content' => 'جهت ثبت و ارسال مقاله، لطفاً نکات زیر را رعایت کنید: محتوای مقاله باید با موضوعات آموزشی و فرهنگی موسسه هم‌خوانی داشته باشد. مقاله‌های شما پس از بررسی و تأیید منتشر می‌شوند.',
            ],
        ];

        foreach ($fields as $key => $field) {
            Section::create([
                'name' => $key,
                'content' => $field['content'],
            ]);
        }

        $Links = [
            'موسسه معصومیه' => 'masoumieh.ir',
        ];

        foreach ($Links as $name => $url) {
            Link::create([
                'name' => $name,
                'link' => $url,
            ]);
        }

    }
}
