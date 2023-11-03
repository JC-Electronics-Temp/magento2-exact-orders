<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin;

use Magento\Framework\Model\AbstractModel;

class StoreAttachmentsFromExternalEntity
{
    public function afterSave(
        AbstractModel $subject,
        AbstractModel $result
    ): AbstractModel {
        $extensionAttributes = $result->getExtensionAttributes();
        $attachments = $extensionAttributes->getAttachments();
    }
}
