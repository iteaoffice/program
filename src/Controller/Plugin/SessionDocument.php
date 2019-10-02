<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\Controller\Plugin;

use Application\Service\AssertionService;
use BjyAuthorize\Service\Authorize;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Image;
use PhpOffice\PhpWord\Writer\Word2007;
use Program\Entity\Call\Session;
use Program\Options\ModuleOptions;
use Project\Entity\Idea\Description;
use Project\Entity\Idea\DescriptionType;
use Zend\Http\Headers;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;
use Zend\View\HelperPluginManager;

/**
 * Class SessionDocument
 *
 * @package Program\Controller\Plugin
 */
final class SessionDocument extends AbstractPlugin
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var PhpWord
     */
    private $document;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var string
     */
    private $headerLogo;
    /**
     * @var string
     */
    private $footerImage;
    /**
     * @var AssertionService
     */
    private $assertionService;
    /**
     * @var Authorize
     */
    private $authorize;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var Url
     */
    private $urlHelper;
    /**
     * @var ServerUrl
     */
    private $serverUrlHelper;

    public function __construct(
        EntityManager $entityManager,
        ModuleOptions $options,
        AssertionService $assertionService,
        Authorize $authorize,
        TranslatorInterface $translator,
        HelperPluginManager $helperPluginManager
    ) {
        $this->entityManager = $entityManager;
        $this->headerLogo = $options->getHeaderLogo();
        $this->footerImage = $options->getFooterImage();
        $this->assertionService = $assertionService;
        $this->authorize = $authorize;
        $this->translator = $translator;

        $this->urlHelper = $helperPluginManager->get(Url::class);
        $this->serverUrlHelper = $helperPluginManager->get(ServerUrl::class);

        Settings::setOutputEscapingEnabled(true);
    }

    public function __invoke(Session $session): SessionDocument
    {
        $this->session = $session;
        $this->document = new PhpWord();
        $this->document->getCompatibility()->setOoxmlVersion(15);
        $this->document->addParagraphStyle('noSpacing', ['spaceBefore' => 0, 'spaceAfter' => 0]);

        $section = $this->document->addSection(
            [
                'marginLeft'   => 500,
                'marginRight'  => 500,
                //'marginTop'    => 200,
                'marginBottom' => 300,
                'headerHeight' => 200,
                'footerHeight' => 80,
            ]
        );

        $header = $section->addHeader();
        if (!empty($this->headerLogo)) {
            $header->addImage(
                $this->headerLogo,
                [
                    'width'            => 180,
                    'positioning'      => Image::POS_ABSOLUTE,
                    'posHorizontal'    => Image::POS_ABSOLUTE,
                    'posHorizontalRel' => Image::POS_RELTO_PAGE,
                    'posVertical'      => Image::POS_ABSOLUTE,
                    'marginLeft'       => 10
                ]
            );
        }

        $footer = $section->addFooter();
        if (!empty($this->footerImage)) {
            $footer->addImage(
                $this->footerImage,
                [
                    'width'            => \round(Converter::cmToPixel(5.2)),
                    'align'            => Image::POS_RIGHT,
                    'positioning'      => Image::POS_ABSOLUTE,
                    'posHorizontal'    => Image::POSITION_HORIZONTAL_RIGHT,
                    'posHorizontalRel' => Image::POS_RELTO_PAGE,
                    'posVertical'      => Image::POSITION_VERTICAL_BOTTOM,
                    'posVerticalRel'   => Image::POS_RELTO_PAGE,
                ]
            );
        }

        $section->addText($session->getSession(), ['bold' => true, 'color' => '00A651', 'size' => 20]);

        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $table->addRow();
        $table->addCell(800)->addText($this->translator->translate('txt-time'), ['bold' => true], 'noSpacing');
        $table->addCell(1600)->addText($this->translator->translate('txt-acronym'), ['bold' => true], 'noSpacing');
        $table->addCell(6800)->addText(
            $this->translator->translate('txt-short-description'),
            ['bold' => true],
            'noSpacing'
        );
        $table->addCell(1600)->addText($this->translator->translate('txt-notes'), ['bold' => true], 'noSpacing');

        foreach ($session->getIdeaSession() as $ideaSession) {
            //Check access to the idea
            $this->assertionService->addResource($ideaSession->getIdea(), \Project\Acl\Assertion\Idea\Idea::class);
            if (!$this->authorize->isAllowed($ideaSession->getIdea(), 'view')) {
                continue;
            }

            $table->addRow(null, ['cantSplit' => true]);
            // Schedule
            $table->addCell(800)->addText($ideaSession->getSchedule(), null, 'noSpacing');
            // Acronym
            $acronymCell = $table->addCell(1600);
            $acronymTextRun = $acronymCell->addTextRun('noSpacing');


            // Acronym / link
            $ideaLink = $this->urlHelper->__invoke('community/idea/view', ['docRef' => $ideaSession->getIdea()->getDocRef()]);
            $acronymTextRun->addLink(
                $this->serverUrlHelper->__invoke() . $ideaLink,
                $ideaSession->getIdea()->parseName(),
                ['underline' => Font::UNDERLINE_SINGLE, 'color' => '00A651'],
                'noSpacing'
            );


            //$acronymTextRun->addText($ideaSession->getIdea()->parseName(), null, 'noSpacing');
            $acronymTextRun->addTextBreak(2);
            $acronymTextRun->addText($this->translator->translate('txt-contact') . ':', ['size' => 8], 'noSpacing');
            $acronymTextRun->addTextBreak();
            $acronymTextRun->addText(
                $ideaSession->getIdea()->getContact()->parseFullName(),
                ['size' => 8],
                'noSpacing'
            );
            // Description
            $shortDescriptionText = '';
            $shortDescription = $this->entityManager->getRepository(Description::class)->findOneBy(
                [
                    'idea' => $ideaSession->getIdea(),
                    'type' => DescriptionType::TYPE_SHORT_DESCRIPTION
                ]
            );
            if ($shortDescription instanceof Description) {
                $shortDescriptionText = \preg_replace(
                    '/[[:cntrl:]]+/',
                    '',
                    \html_entity_decode(\strip_tags($shortDescription->getDescription()))
                );
            }
            $table->addCell(6800)->addText($shortDescriptionText, ['size' => 9], 'noSpacing');
            // Notes
            $table->addCell(1600)->addText('', ['size' => 9], 'noSpacing');
        }

        return $this;
    }

    public function parseResponse(): Response
    {
        $response = new Response();
        if (!($this->document instanceof PhpWord)) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        /** @var Word2007 $writer */
        $writer = IOFactory::createWriter($this->document, 'Word2007');

        \ob_start();
        $gzip = false;
        // Gzip the output when possible. @see http://php.net/manual/en/function.ob-gzhandler.php
        if (\ob_start('ob_gzhandler')) {
            $gzip = true;
        }
        $writer->save('php://output');
        if ($gzip) {
            \ob_end_flush(); // Flush the gzipped buffer into the main buffer
        }
        $contentLength = \ob_get_length();

        // Prepare the response
        $response->setContent(\ob_get_clean());
        $response->setStatusCode(Response::STATUS_CODE_200);
        $headers = new Headers();
        $headers->addHeaders(
            [
                'Content-Disposition' => 'attachment; filename="' . $this->session->getSession() . '.docx"',
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Length'      => $contentLength,
                'Expires'             => '0',
                'Cache-Control'       => 'must-revalidate',
                'Pragma'              => 'public',
            ]
        );
        if ($gzip) {
            $headers->addHeaders(['Content-Encoding' => 'gzip']);
        }
        $response->setHeaders($headers);

        return $response;
    }

    public function getDocument(): PhpWord
    {
        return $this->document;
    }
}
