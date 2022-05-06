<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelevanceAnalysisConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relevance_analysis_config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('count_sites')->nullable();
            $table->longText('region')->nullable();
            $table->text('ignored_domains')->nullable();
            $table->integer('separator')->nullable();
            $table->string('noindex')->nullable();
            $table->string('meta_tags')->nullable();
            $table->string('parts_of_speech')->nullable();
            $table->string('remove_my_list_words')->nullable();
            $table->string('my_list_words')->nullable();
            $table->string('hide_ignored_domains')->nullable();
            $table->integer('ltp_count')->nullable();
            $table->integer('ltps_count')->nullable();
            $table->integer('scanned_sites_count')->nullable();
            $table->integer('recommendations_count')->nullable();
            $table->integer('boostPercent')->nullable();
        });

        $config = new \App\RelevanceAnalysisConfig([
            'count_sites' => 10,
            'region' => 1,
            'separator' => 3,
            'noindex' => 'no',
            'meta_tags' => 'no',
            'parts_of_speech' => 'no',
            'remove_my_list_words' => 'no',
            'hide_ignored_domains' => 'no',
            'ltp_count' => '10',
            'ltps_count' => '10',
            'scanned_sites_count' => '10',
            'recommendations_count' => '10',
            'boostPercent' => '0',
            'ignored_domains' =>
                "2gis.ru\n" .
                "aliexpress.com\n" .
                "AliExpress.ru\n" .
                "auto.ru\n" .
                "avito.ru\n" .
                "banki.ru\n" .
                "beru.ru\n" .
                "blizko.ru\n" .
                "cataloxy.ru\n" .
                "deal.by\n" .
                "domclick.ru\n" .
                "ebay.com\n" .
                "edadeal.ru\n" .
                "e-katalog.ru\n" .
                "hh.ru\n" .
                "instagram.com\n" .
                "irecommend.ru\n" .
                "irr.ru\n" .
                "leroymerlin.ru\n" .
                "market.yandex.ru\n" .
                "mvideo.ru\n" .
                "onliner.by\n" .
                "otzovik.com\n" .
                "ozon.ru\n" .
                "pandao.ru\n" .
                "price.ru\n" .
                "prodoctorov.ru\n" .
                "profi.ru\n" .
                "pulscen.ru\n" .
                "quto.ru\n" .
                "rambler.ru\n" .
                "regmarkets.ru\n" .
                "satom.ru\n" .
                "shop.by\n" .
                "sravni.ru\n" .
                "tiu.ru\n" .
                "toshop.ru\n" .
                "wikipedia.org\n" .
                "wildberries.ru\n" .
                "yandex.ru\n" .
                "yell.ru\n" .
                "zoon.ru\n"
        ]);
        $config->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relevance_analysis_config');
    }
}
