import React from 'react';
import ShopSingleCard from '../common/ShopSingleCard';
import productsData from '@/data/products-data';

const FeatureBookArea = () => {
    return (
        <>
            {/* -- feature book area start -- */}
            <div className="bd-feature-book-area section-space-bottom">
                <div className="container">
                    <div className="bd-section-bg section-title-space">
                        <div className="row gy-30 justify-content-between align-items-center">
                            <div className="col-xl-4 col-lg-5 col-md-4">
                                <div className="bd-section-wrapper">
                                    <h2 className="bd-section-title x-small">Featured Books</h2>
                                </div>
                            </div>
                            <div className="col-xl-5 col-lg-6 col-md-8">
                                <div className="bd-featured-book-tab">
                                    <div className="tab-style-five">
                                        <ul className="nav nav-pills justify-content-md-end" id="pills-tab" role="tablist">
                                            <li className="nav-item" role="presentation">
                                                <button className="nav-link active" id="pills-featured-book-tab" data-bs-toggle="pill"
                                                    data-bs-target="#pills-featured-book" type="button" role="tab" aria-controls="pills-featured-book" aria-selected="true">Featured</button>
                                            </li>
                                            <li className="nav-item" role="presentation">
                                                <button className="nav-link" id="pills-sale-book-tab" data-bs-toggle="pill" data-bs-target="#pills-sale-book" type="button" role="tab" aria-controls="pills-sale-book" aria-selected="false">Deal of the
                                                    Day</button>
                                            </li>
                                            <li className="nav-item" role="presentation">
                                                <button className="nav-link" id="pills-popular-book-tab" data-bs-toggle="pill" data-bs-target="#pills-popular-book" type="button" role="tab" aria-controls="pills-popular-book" aria-selected="false">Popular
                                                    Picks</button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-xl-12">
                            <div className="tab-style-one">
                                <div className="tab-content" id="pills-tabContent">
                                    <div className="tab-pane fade show active" id="pills-featured-book" role="tabpanel" aria-labelledby="pills-featured-book-tab" tabIndex={0}>
                                        <div className="row gy-30">
                                            {
                                                productsData.slice(0, 8).map((item) => (
                                                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6" key={item.id}>
                                                        <ShopSingleCard item={item} />
                                                    </div>
                                                ))
                                            }
                                        </div>
                                    </div>
                                    <div className="tab-pane fade" id="pills-sale-book" role="tabpanel" aria-labelledby="pills-sale-book-tab" tabIndex={0}>
                                        <div className="row gy-30">
                                            {
                                                productsData.slice(0, 8).map((item) => (
                                                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6" key={item.id}>
                                                        <ShopSingleCard item={item} />
                                                    </div>
                                                ))
                                            }
                                        </div>
                                    </div>
                                    <div className="tab-pane fade" id="pills-popular-book" role="tabpanel" aria-labelledby="pills-popular-book-tab" tabIndex={0}>
                                        <div className="row gy-30">
                                            {
                                                productsData.slice(0, 8).map((item) => (
                                                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6" key={item.id}>
                                                        <ShopSingleCard item={item} />
                                                    </div>
                                                ))
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* -- feature book area end -- */}
        </>
    );
};

export default FeatureBookArea;