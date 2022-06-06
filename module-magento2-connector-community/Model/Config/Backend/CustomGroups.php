<?php

namespace Akeneo\Connector\Model\Config\Backend;

use Akeneo\Connector\Block\Adminhtml\System\Config\Form\Field\Grouped;

class CustomGroups extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    protected $grouped;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        Grouped $grouped
    ){
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->grouped = $grouped;
    }

    public function beforeSave()
    {
        $families = $this->grouped->getFamilies();
        $groupedFamilies = [];
        $value = $this->getValue();
        if(is_array($value)){
            unset($value['__empty']);
        }
        $value = array_values($value);
        if(!$value){
            foreach($families as $familyCode => $label){
                $associationCode = $familyCode;
                // check that the length is within sql column identifier character limit
                if(strlen($familyCode)>55){
                    $associationCode = substr($familyCode, -55);
                }
                if(str_contains($familyCode, "_group")){
                    array_push($groupedFamilies, ["akeneo_grouped_family_code" => $familyCode, "akeneo_quantity_association" => $associationCode]);
                }   
            }
        }
        else{
            $groupedFamilies = $value;
        }
        $result = json_encode($groupedFamilies, JSON_FORCE_OBJECT);
        $this->setValue($result);
        return parent::beforeSave();
    }
}