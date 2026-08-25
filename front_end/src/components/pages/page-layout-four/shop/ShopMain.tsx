"use client"
import React, { useState } from 'react';
import Breadcrumbs from '../../../common/Breadcrumb/Breadcrumbs';
import ProductFeatures from './ProductFeatures';
import Language from './Language';
import ProductFormat from './ProductFormat';
import ShopAuthor from './ShopAuthor';
import ShopBookCategories from './ShopBookCategories';
import ProductPrice from './ProductPrice';
import BasicPagination from '../../../elements/pagination/BasicPagination';
import productsData from '@/data/products-data';
import ShopSingleCard from '../../../common/ShopSingleCard';
import ShopListSingleCard from '../../../common/ShopListSingleCard';
import { productOrderBy } from '@/data/dropdown-data';
import NiceSelect from '@/components/elements/nice-select/NiceSelect';

const ShopMain = () => {
    const [isGridView, setIsGridView] = useState(true);

    const handleGridClick = () => {
        setIsGridView(true);
    };

    const handleListClick = () => {
        setIsGridView(false);
    };
    const selectHandler = () => { }
    
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Shop' />
            {/* -- shop page area start -- */}
            <section className="bd-shop-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-xxl-9 col-xl-9 col-lg-8">
                            <div className="bd-sorting-meta d-flex-between flex-wrap mb-30 gap-30">
                                <div className="bd-top-sorting-left">
                                    <h6 className="bd-sorting-item-found">We found <span>9</span> books available for you</h6>
                                </div>
                                <div className="bd-top-sorting-right d-flex align-items-center gap-15">
                                    <div className="bd-layout-switcher">
                                        <label className={`bd-filter-type-text bd-list-filter-text ${!isGridView ? "active" : ""}`}>
                                            List
                                        </label>
                                        <label className={`bd-filter-type-text bd-grid-filter-text ${isGridView ? "active" : ""}`}>
                                            Grid
                                        </label>
                                        <ul className="bd-switcher-btn">
                                            <li>
                                                <button onClick={handleGridClick} 
                                                className={`bd-filter-layout-trigger bd-grid-filter-trigger ${isGridView ? "active" : ""}`}>
                                                    <i className="fa-solid fa-grid"></i>
                                                </button>
                                            </li>
                                            <li>
                                                <button onClick={handleListClick}
                                                className={`bd-filter-layout-trigger bd-list-filter-trigger ${!isGridView ? "active" : ""}`}>
                                                    <i className="fa-solid fa-list"></i>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div className="bd-sorting-select">
                                        <NiceSelect
                                            options={productOrderBy}
                                            defaultCurrent={0}
                                            onChange={selectHandler}
                                            filterIcon={true}
                                            name=""
                                            className="product-orderby"
                                        />
                                    </div>
                                </div>
                            </div>
                            {/* -- product grid style -- */}
                            <div className={`display-layout-grid ${isGridView ? "active" : ""}`} style={{ height: isGridView ? "auto" : "0", }}>
                                <div className="row gy-30">
                                    {
                                        productsData.slice(13, 31).map((item) => (
                                            <div className="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6" key={item.id}>
                                                <ShopSingleCard item={item} />
                                            </div>
                                        ))
                                    }
                                </div>
                            </div>
                            {/* -- product grid style end -- */}

                            {/* -- product list style -- */}
                            <div className={`display-layout-list ${!isGridView ? "active" : ""}`} style={{ height: !isGridView ? "auto" : "0",}}>
                                <div className="bd-product-list-wrapper w-100">
                                    {
                                        productsData.slice(31, 48).map((item) => (
                                            <ShopListSingleCard item={item} key={item.id} />
                                        ))
                                    }
                                </div>
                            </div>
                            {/* -- product list style end -- */}

                            {/* -- pagination style -- */}
                            <BasicPagination />
                            {/* -- pagination style end -- */}
                        </div>
                        <div className="col-xxl-3 col-xl-3 col-lg-4">
                            <div className="bd-shop-sidebar sidebar-right sidebar-sticky">
                                <ShopBookCategories />
                                <ProductPrice />
                                <ShopAuthor />
                                <ProductFormat />
                                <Language />
                                <ProductFeatures />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- shop page area end -- */}
        </>
    );
};

export default ShopMain;