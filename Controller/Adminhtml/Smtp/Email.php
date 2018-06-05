<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Smtp
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Smtp\Controller\Adminhtml\Smtp;

use Magento\Backend\App\Action;
use Mageplaza\Smtp\Model\LogFactory;
use Magento\Backend\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Email
 * @package Mageplaza\Smtp\Controller\Adminhtml\Smtp
 */
class Email extends Action
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LogFactory
     */
    protected $logFactory;

    /**
     * Email constructor.
     * @param Context $context
     * @param LogFactory $logFactory
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        LogFactory $logFactory,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);

        $this->logFactory = $logFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $logId = $this->getRequest()->getParam('id');

        if (!$logId) {
            $this->_redirect('*/smtp/log');
            return;
        }

        $email = $this->logFactory->create();
        $data = $email->load($logId)->getData();
        $data['email_content'] = htmlspecialchars_decode($data['email_content']);

        $status = $email->resendEmail($data);

        if ($status) {
            $this->messageManager->addSuccess(
                __('Email re-sent successfully!')
            );
        }
        $this->_redirect('*/smtp/log');
        return;
    }
}