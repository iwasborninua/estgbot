<?php

namespace App\Telegram\Commands;

use App\Telegram\Keyboards\Keyboards;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

class FaqCommand extends Command
{
    protected string $name = 'faq';

    protected string $description = 'Часто задаваемуе вопросы';

    public function handle()
    {
        $text = 'Q: У вас можна придбати "готовий продукт"?
A: Категорічно ні! Ми ніяк не пов\'язані з коноплею та торгівлею нею. Замовити у нас її не можна. Ми не порушуємо закону.

Q: Чи можна сплатити замовлення при отриманні?
A: Можна, але тільки в таких випадках:
1) Сума замовлення складає щонайменше 400 гривень.
2) Доставка замовлення здійснюватиметься транспортною компанією “Нова Пошта”
При доставці "Інтаймом" або "Укрпоштою" - оплата післяплатою неможлива!

Q: Я можу оформити замовлення, не вказуючи реальні дані про себе?
A: Теоретично так, але майте на увазі, що при її отриманні в будь-якому відділенні УкрПошти, Нової Пошти, Інтайму у вас вимагатимуть документ, що підтверджує особу одержувача. Тому, щоб уникнути будь-яких неприємностей, переконливе прохання при замовленні вказувати реальне прізвище та ім\'я одержувача.

Q: На які види ділиться насіння?
A: Фемінізоване насіння - 100% жіночі рослини. Тобто можливість появи чоловічої рослини 0%. Відповідно, регуляри - насіння, в якому відсоткове співвідношення чоловічих/жіночих генів становить 50/50. Тому замовляючи 5 насіння-регулярів не варто чекати 5 рослин жіночої статі. За більш детальним описом видів насіння звертайтесь на форум (https://jahforum.org/). Далі вищезгадані види поділяються на підвиди - автоквітучі та фотоперіодні. Насіння-автоквіти: здебільшого це невисокі (метр в середньому) рослини, період збору врожаю яких в середньому дорівнює 2.5 місяцям, а також вони не вимагають введення світлового режиму при вирощуванні в індорі. Насіння-фотоперіоди: пряма протилежність автоквітам - найчастіше вони на порядок вищі, період збирання врожаю становить близько півроку. Тепер вибір за вами: від автоквіту ви отримаєте швидкість і компактність, але значно менший урожай, а від фотоперіодичного сорту розмір рослини і, як наслідок, високу врожайність.

Q: Надішліть мені інструкцію з вирощування (одне з найпоширеніших питань при обробці електронної пошти)
A: З усіх питань вирощування, у тому числі і з тієї самої “інструкції”, ми радимо звертатися на наш форум https://jahforum.net/. На ньому ви знайдете безліч таких тем і розділів як "Для новачків", "Юридичний розділ", "Новини", "Репорти" та багато іншого. Таким чином, в одному з відповідних розділів ви зможете задати будь-яке питання, на яке вам докладно дадуть відповідь тямущі, досвідчені люди.

Q: У якій упаковці до мене прийде насіння і чи буде воно підписане?
A: Хоча саме насіння і не переслідується законодавством, ми намагаємося упаковувати посилку максимально непомітно та надійно. Ми відправляємо насіння у наших оригінальних упаковках фасуваннями по 1, 3, 5 або 10 штук. Коробка з насінням пакується в кур\'єрський пакет. Якщо чогось із складових для пакування немає, ми завжди компенсуємо це додатковими бонусами. Це щодо відправки посилок

Q: А які гарантії ви можете надати?
A: Тут слід розділяти речі, які ми можемо гарантувати і які, на жаль, не можемо. Таким чином, ми можемо гарантувати:

- Якість насіння. Теми про їхнє вирощування різними людьми постійно з\'являються на форумі. Також, перш ніж потрапити на вітрину, вони тестуються за умов максимально наближених до ідеальних.
- Факт відправлення насіння.

І ми не можемо гарантувати:

- Успішну доставку насіння. Ми намагаємося зробити упаковку максимально безпечною та надаємо вам номер декларації чи трек-накладної. Що свідчить, що ми зробили все можливе з нашого боку. Всі інші питання доставки - до перевізника.
- Схожість насіння після того, як воно потрапило до вас. Все залежить від багатьох таких факторів як умови/середовище вирощування, знання, досвід і т.п.

Q: Чому з вами неможливо зв\'язатися?
A: Ми працюємо 24/7:
Ви можете зробити замовлення онлайн онлайн у будь-який час доби. Ваше замовлення буде оброблено протягом 5-10 хвилин!
Замовлення надсилається протягом 24 годин після оплати.

Ви можете відстежити свій лист на сайті Нової Пошти
https://novaposhta.ua/tracking';


        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => Keyboards::mainMenuKeyboard() // Используем клавиатуру из Keyboards.php
        ]);
    }
}
// Compare this snippet from app/Telegram/Commands/StartCommand.php:
