<?php

namespace App\Classes\phpMorphy;

use phpMorphy;
use phpMorphy_FilesBundle;

require_once 'src/common.php';

class Common
{
    /**
     * @var phpMorphy
     */
    private $en;

    /**
     * @var phpMorphy
     */
    private $ru;

    public function __construct($storage = 'file')
    {
        $this->ru = new phpMorphy(
            new phpMorphy_FilesBundle('rus'),
            array('storage' => $storage));

        $this->en = new phpMorphy(
            new phpMorphy_FilesBundle('eng'),
            array('storage' => $storage)
        );
    }

    /**
     * @param string $word to get base from
     * @return string
     */
    public function base(string $word): ?string
    {
        $sanitizedWord = $this->sanitize($word);
        $result = $this->getMorphy($sanitizedWord)->getBaseForm($sanitizedWord);

        return $result ? mb_strtolower(array_shift($result), 'UTF-8') : null;
    }

    /**
     * @param string $word to sanitize
     * @return string
     */
    private function sanitize(string $word): string
    {
        return mb_strtoupper(trim($word), 'UTF-8');
    }

    /**
     * @param string $word
     * @return phpMorphy
     */
    private function getMorphy(string $word): phpMorphy
    {
        return $this->isRussian($word) ? $this->ru : $this->en;
    }

    /**
     * @param string $word
     * @return bool
     */
    private function isRussian(string $word): bool
    {
        return (bool)preg_match('/[А-Я]/u', $word);
    }
}
