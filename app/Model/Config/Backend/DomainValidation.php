<?php
/**
 * Created by PhpStorm.
 * Date: 9/15/17
 */

namespace Okitcom\OkLibMagento\Model\Config\Backend;


use Magento\Config\Model\Config\Backend\File;

class DomainValidation extends File
{

    /**
     * Dont upload the file, only save the filename
     * @return $this
     */
    public function beforeSave() {
        $value = $this->getValue();
        $file = $this->getFileData();
        if (!empty($file)) {
            $filename = $file["name"];
            if ($filename) {
                $this->setValue($filename);
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                $this->setValue('');
            } elseif (is_array($value) && !empty($value['value'])) {
                $this->setValue($value['value']);
            } else {
                $this->unsValue();
            }
        }

        return $this;
    }


}