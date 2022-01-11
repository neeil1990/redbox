<?php
namespace App\Http\Controllers;

use App\Classes\Xml\XmlFacade;

class TestController extends Controller
{
    /**
     *
     * path results response/results/grouping/group/
     */
    public function index()
    {

        /**
         * default
         *
         * https://xmlstock.com/yandex/xml/
         */
        $xmlstock = new XmlFacade();
        $xmlstock->setQuery('Привет мир!');

        // Number pages
        $xmlstock->setPage(0);

        $array_1 = $xmlstock->getByArray();
        $obj_1 = $xmlstock->getByObject();

        // Number pages
        $xmlstock->setPage(1);

        $array_2 = $xmlstock->getByArray();
        $obj_2 = $xmlstock->getByObject();

        dump($array_1, $array_2);


        /**
         * Пример быстрой записи
         */
        $xml = (new XmlFacade())->setQuery('Hello world!')->getByArray();
        dump($xml);


        /**
         *
         * https://xmlproxy.ru/search/xml
         */
        $xmlproxy = new XmlFacade();
        $xmlproxy->setPath('https://xmlproxy.ru/search/xml');
        $xmlproxy->setUser('sv@prime-ltd.su');
        $xmlproxy->setKey('2fdf7f2b218748ea34cf1afb8b6f8bbb');

        /**
         * Поисковый запрос
         *
         */
        $xmlproxy->setQuery('Привет мир еще раз!');

        /**
         * Регионы поиска
         * #https://yandex.ru/dev/xml/doc/dg/reference/regions.html
         */
        $xmlproxy->setLr('20');

        /**
         * Получение результата в формате массива и объекта.
         *
         */
        $arrayProxy = $xmlproxy->getByArray();
        $objProxy = $xmlproxy->getByObject();

        dump($arrayProxy, $objProxy);
    }
}
