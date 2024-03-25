<?php                                      
                                                     
namespace App\Service;

use App\Entity\Config;
use App\Entity\FaqBlock;
use App\Entity\FaqHighlighted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FaqService
{
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;
    private ?string $label = null;
    private array $faqs = [];

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    public function generate(string $locale): self
    {
        $isAuto = $this->isFaqAuto();
        if ($isAuto) {
            $this->label = $this->translator->trans('front.faq.recent');
            $this->faqs = $this->em
                ->getRepository(FaqBlock::class)
                ->findLastUpdatedByLocale($locale);
        } else {
            $this->label = $this->translator->trans('front.faq.frequent');
            $this->faqs = $this->getFaqFromHighligted($locale);
        }

        return $this;
    }

    private function getFaqFromHighligted(string $locale): array
    {
        $faqs = [];
        $faqHighlighted = $this->em
            ->getRepository(FaqHighlighted::class)
            ->findOrderByWeightWithFaqBlockInLocale($locale);
        /** @var FaqHighlighted $faqElement */
        foreach ($faqHighlighted as $faqElement) {
            $faqs[] = $faqElement->getFaq();
        }

        return $faqs;
    }

    private function isFaqAuto(): bool
    {
        $configFaqHighligted = $this->em
            ->getRepository(Config::class)
            ->findOneBy(['name' => FaqHighlighted::NAME_AUTO]);

        if (!$configFaqHighligted instanceof Config) {
            return false;
        }
        if (isset($configFaqHighligted->getData()['auto'])) {
            return (bool)$configFaqHighligted->getData()['auto'];
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getFaqs(): array
    {
        return $this->faqs;
    }
}
