"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import ShopListSingleCard from '@/components/common/ShopListSingleCard';
import ShopSingleCard from '@/components/common/ShopSingleCard';
import NiceSelect from '@/components/elements/nice-select/NiceSelect';
import { productOrderBy } from '@/data/dropdown-data';
import productsData from '@/data/products-data';
import Link from 'next/link';
import React, { useState } from 'react';

const ShopTwoMain = () => {
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
            <Breadcrumbs breadcrumbTitle='Shop v2' />
            {/* -- shop page v2 area start -- */}
            <section className="bd-shop-page-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xxl-12">
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
                                            <div className="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-6" key={item.id}>
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
                                        productsData.slice(48, 65).map((item) => (
                                            <ShopListSingleCard item={item} key={item.id} />
                                        ))
                                    }
                                </div>
                            </div>
                            {/* -- product list style end -- */}

                            {/* -- pagination style -- */}
                            <div className="basic-pagination">
                                <nav>
                                    <ul>
                                        <li><Link href="#" className="prev"><i className="fa-solid fa-angle-left"></i></Link></li>
                                        <li><Link href="#" className="current">1</Link></li>
                                        <li><Link href="#">2</Link></li>
                                        <li><Link href="#">3</Link></li>
                                        <li><Link href="#" className="next"><i className="fa-solid fa-angle-right"></i></Link></li>
                                    </ul>
                                </nav>
                            </div>
                            {/* -- pagination style end -- */}
                        </div>
                    </div>
                </div>
            </section>
            {/* -- shop page v2 area end -- */}
        </>
    );
};

export default ShopTwoMain;