<?php
declare(strict_types=1);

namespace Snowdog\WishlistUnlocker\CustomerData;

use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Wishlist\Block\Customer\Sidebar;
use Magento\Wishlist\CustomerData\Wishlist as MageWishlist;
use Magento\Wishlist\Helper\Data;


class Wishlist extends MageWishlist
{
    const ITEMS_LIMIT_CONFIG = 'wishlist/general/items_limit';
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Wishlist constructor.
     * @param Data $wishlistHelper
     * @param Sidebar $block
     * @param ImageFactory $imageHelperFactory
     * @param ViewInterface $view
     * @param ItemResolverInterface|null $itemResolver
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Data $wishlistHelper,
        Sidebar $block,
        ImageFactory $imageHelperFactory,
        ViewInterface $view,
        ItemResolverInterface $itemResolver = null,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($wishlistHelper, $block, $imageHelperFactory, $view, $itemResolver);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get wishlist items
     *
     * @return array
     */
    protected function getItems(): array
    {
        $this->view->loadLayout();
        $collection = $this->wishlistHelper->getWishlistItemCollection();
        $collection->clear()->setPageSize($this->getItemsLimit())
            ->setInStockFilter(true)->setOrder('added_at');

        $items = [];
        foreach ($collection as $wishlistItem) {
            $items[] = $this->getItemData($wishlistItem);
        }
        return $items;
    }

    /**
     * @return int
     */
    private function getItemsLimit(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::ITEMS_LIMIT_CONFIG,
            ScopeInterface::SCOPE_STORE
        );
    }
}
