<?php
namespace App\Service;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\String\u;
class SlugService
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function slug($word)
    {
        $slug = $this->slugger->slug(u($word)->lower());
        return $slug;
    }
}
