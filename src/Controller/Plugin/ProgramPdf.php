<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Invoice
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/invoice for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Controller\Plugin;

use InvalidArgumentException;
use setasign\Fpdi\Tcpdf\Fpdi;

/**
 * Class InvoicePdf
 *
 * @package Invoice\Controller\Plugin
 */
final class ProgramPdf extends Fpdi
{
    private ?string $tplIdx = null;
    private ?string $template = null;

    public function header(): void
    {
        if (null === $this->tplIdx) {
            if (! file_exists($this->template)) {
                throw new InvalidArgumentException(sprintf('Template %s cannot be found', $this->template));
            }
            $this->setSourceFile($this->template);
            $this->tplIdx = $this->importPage(1);
        }
        $this->useTemplate($this->tplIdx);
        $this->setHeaderMargin($this->GetY() + 5); // padding for second page

        $this->SetTextColor();
        $this->SetXY(15, 5);
    }

    public function footer(): void
    {
        // empty method body
    }

    public function setTemplate($template): ProgramPdf
    {
        $this->template = $template;

        return $this;
    }
}
