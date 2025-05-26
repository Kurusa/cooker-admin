<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Routing\Controller as BaseController;

class DebugController extends BaseController
{
    public function __construct()
    {
    }

    public function debug()
    {

        $client = new Client();
        $r = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=AIzaSyAE3cxYRJiDIoYb9X89N3e3Q_DU1xIg2mI', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "contents" => [
                    'parts' => [
                        "text" => "На цій сторінці можуть бути один або кілька рецептів.Розпізнай кожен із них і поверни масив об’єктів у форматі JSON без жодних описів і пояснень.Кожен об’єкт має ключі:
-title(string)
-categories(array<string>):категорії без дублювання(це обов'язкове поле,тож визнач сам,якщо не вказано)
-complexity(string):easy|medium|hard(визнач сам,якщо не вказано)
-cookingTime(int):загальний час у хвилинах
-portions(int):кількість порцій(визнач сам,якщо не вказано)
-image(string):URL головного зображення рецепта
-ingredientGroups(array<object>):якщо інгредієнти розбиті по групам,кожен такий розділ–окремий об’єкт з ключами:-group (string)–назва групи(наприклад, 'для тіста'),-ingredients (array<object>):title,unit,quantity(завжди int або float,це обовязково.не може бути стрінгою.дроби переводь у float)
Якщо груп немає–поверни один елемент зі group =''та всі інгредієнти всередині
-cuisines(array<string>):кухня страви(це обов'язкове поле,тож визнач сам,якщо не вказано)
-steps(array<object>):кожен об’єкт має description,image
Обов’язкові правила:
Уніфікуй одиниці виміру лише за написанням,НЕ конвертуючи значення(г,кг,мл,л,ч.л,ст.л,склянка,чашка тощо;cups не перетворюй у л,а у склянки).
'За бажанням'-пропускай,це не юніт.
Завжди перекладай українською,у називному відмінку,нижній регістр,без дублікатів.
Якщо кількість є,а одиниці немає і це штуковий інгредієнт(яйця,огірки тощо),став unit=шт
Для спецій типу сіль,перець тощо,якщо кількість та одиниця не вказані — не додавай quantity і unit
Прибери будь-які префікси виду крок N або їхні варіації на початку описів кроків
Ігноруй неінформативні кроки,що містять лише слова на кшталт “Подаємо”,“Смачного”,“Enjoy”
Якщо на сторінці один рецепт поверни масив із одним об’єктом. Ось HTML:" . '<div><div><div><a href="/appetizer/"><span>Закуски</span></a></div><h1>Яйця, фаршировані сиром та горіхами</h1><div><span>Автор <a href="https://jisty.com.ua/author/jistyadmin/">Максим Нікітін</a></span><span>31 Серпня о 00:00</span><span><i></i>563</span><span><i></i>Коментарів немає</span></div><div><img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI5MDAiIGhlaWdodD0iNjAwIiB2aWV3Qm94PSIwIDAgOTAwIDYwMCI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgc3R5bGU9ImZpbGw6I2NmZDRkYjtmaWxsLW9wYWNpdHk6IDAuMTsiLz48L3N2Zz4=" data-src="/wp-content/uploads/2019/11/yajtsya-farshirovani-sirom-ta-gorihami.jpg"></div></div><div><p>У цьому рецепті ви дізнаєтеся, як перетворити звичайні яйця у святкову страву. До того ж вона готується дуже швидко. Не дивно, що фаршировані яйця є однією з найпопулярніших закусок.</p><div><div><a href="#wpzoom-block-ingredients-157961731306723"><img data-src="/wp-content/plugins/recipe-card-blocks-by-wpzoom/dist/assets/images/printer.svg">Print</a></div><h3>Інгредієнти для страви</h3><ul><li><p> 6 <a rel="" href="https://jisty.com.ua/yaki-kuryachi-yajtsya-kupuvati-bili-chi-korichnevi/" target="_blank">яєць</a></p></li><li><p> 50 грамів плавленого сиру</p><div><ins></ins></div></li><li><p> 5 волоських горіхів</p></li><li><p> 2 столові ложки майонезу</p></li><li><p> 2 зубки часнику</p></li></ul></div><p> Спробуйте також <a href="https://jisty.com.ua/yajtsya-farshirovani-z-chervonoyu-ikroyu/">рецепт фаршированих яєць з червоною ікрою</a>.</p><div><div><a href="#wpzoom-block-directions-157961735984128"><img data-src="/wp-content/plugins/recipe-card-blocks-by-wpzoom/dist/assets/images/printer.svg">Print</a></div><h3> Як приготувати фаршировані яйця</h3><ul><li> Яйця зварити круто. Коли охолонуть – почистити, розрізати кожне уздовж наполовину.</li><li> Жовтки вийняти, розім’яти виделкою. Додати до них дрібно тертий сир, подрібнені часник і горіхи.</li><li> Масу заправити майонезом, ретельно вимішати і наповнити нею половинки білків.</li><li> Прикрасити можна зеленню або цілими половинками чи четвертинками горіхів, які покласти на кожну половинку яйця.</li></ul></div><p> Радимо ще <a href="https://jisty.com.ua/pirig-z-yajtsyami-i-zelenoyu-tsibuleyu/">приготувати пиріг з яйцями і зеленою цибулею</a>.</p><div><ins></ins></div><div><div><span><i></i>Оновлено: 21 Січня о 16:36</span><span><i></i>Коментарів немає</span></div><div><div><span><i></i>Теги: </span><a href="https://jisty.com.ua/tag/gretski-gorihi/" rel="tag">грецькі горіхи</a><a href="https://jisty.com.ua/tag/majonez/" rel="tag">майонез</a><a href="https://jisty.com.ua/tag/plavlenij-sir/" rel="tag">плавлений сир</a><a href="https://jisty.com.ua/tag/chasnik/" rel="tag">часник</a><a href="https://jisty.com.ua/tag/yajtsya/" rel="tag">яйця</a></div></div><div><a href="https://www.youtube.com/channel/UCuR_LZE7mxatheEmHJgz6xA?sub_confirmation=1" target="_blanck"><img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI5NzAiIGhlaWdodD0iMjUwIiB2aWV3Qm94PSIwIDAgOTcwIDI1MCI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgc3R5bGU9ImZpbGw6I2NmZDRkYjtmaWxsLW9wYWNpdHk6IDAuMTsiLz48L3N2Zz4=" data-src="/wp-content/uploads/2021/01/yisty-baner.png"></a></div></div><div><div><a href="https://jisty.com.ua/author/jistyadmin/"><img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMjMiIGhlaWdodD0iMjIzIiB2aWV3Qm94PSIwIDAgMjIzIDIyMyI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgc3R5bGU9ImZpbGw6I2NmZDRkYjtmaWxsLW9wYWNpdHk6IDAuMTsiLz48L3N2Zz4=" data-src="/wp-content/litespeed/avatar/0d43dd9ae6c974f332a011ca97a9e417.jpg?ver=1748253801"></a></div><div><div><span><span><a href="https://jisty.com.ua/author/jistyadmin/" rel="author">Максим Нікітін</a></span></span></div><div>Я не професійний кухар, але люблю проводити час на кухні.Я не є автором всіх текстів, але відповідаю за їх якість.</div><a href="https://jisty.com.ua/author/jistyadmin/">Усі публікації автора</a><div><ul><li><a href="https://www.facebook.com/MassimoUA" target="_blank"><i></i></a></li><li><a href="https://www.instagram.com/massimoua/" target="_blank"><i></i></a></li></ul></div></div></div><noscript><ul></ul></noscript></div></div>'
                    ]
                ]
            ]
        ]);

        dd($r->getBody()->getContents());
    }
}
