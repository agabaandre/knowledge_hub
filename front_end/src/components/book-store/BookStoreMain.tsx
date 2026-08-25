import React from 'react';
import ShopCategoriesSlider from '../sliders/category/ShopCategoriesSlider';
import FeatureBookArea from './FeatureBookArea';
import DiscountBookArea from '../sliders/product/DiscountBookArea';
import TradingAreaStart from '../sliders/product/TradingAreaStart';
import SelerAuthorSlider from '../sliders/SelerAuthorSlider';
import NewsletterArea from './NewsletterArea';
import ShopBlogAreaSlider from '../sliders/blog/ShopBlogAreaSlider';
import BookCtaArea from './BookCtaArea';
import BookStoreBannerArea from './BookStoreBannerArea';

const BookStoreMain = () => {
    return (
        <>
            <BookStoreBannerArea/>
            <ShopCategoriesSlider/>
            <FeatureBookArea/>
            <DiscountBookArea/>
            <BookCtaArea/>
            <TradingAreaStart/>
            <SelerAuthorSlider/>
            <NewsletterArea/>
            <ShopBlogAreaSlider/>
        </>
    );
};

export default BookStoreMain;