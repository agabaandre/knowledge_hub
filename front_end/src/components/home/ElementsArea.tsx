import elementsData from '@/data/elements-categories-data';
import Link from 'next/link';
import React from 'react';

const ElementsArea = () => {
    return (
        <>
            {/* -- elements area start -- */}
            <section className="element-area fix section-space-bottom">
                <div className="bd-present-area">
                    <div className="container-fluid">
                        <div className="row justify-content-center text-center">
                            <div className="col-xl-6 col-lg-8">
                                <div className="section-title-wrapper text-center section-title-space">
                                    <span className="bd-section-subtitle">Key Features</span>
                                    <h2 className="bd-section-title">350+ Premium Learning Elements</h2>
                                </div>
                            </div>
                        </div>
                        <div className="row elements-category-list">
                            {
                                elementsData.slice(0, 24).map((item) => (
                                    <Link className="single-elements" href="#" key={item.id}>
                                        <i className={item.icon}></i>
                                        {item.label}
                                    </Link>
                                ))
                            }
                        </div>
                        <div className="row elements-category-list elements-category-list-2 mt-20">
                            {
                                elementsData.slice(24, 48).map((item) => (
                                    <Link className="single-elements" href="#" key={item.id}>
                                        <i className={item.icon}></i>
                                        {item.label}
                                    </Link>
                                ))
                            }
                        </div>
                        <div className="row elements-category-list mt-20">
                            {
                                elementsData.slice(0, 24).map((item) => (
                                    <Link className="single-elements" href="#" key={item.id}>
                                        <i className={item.icon}></i>
                                        {item.label}
                                    </Link>
                                ))
                            }
                        </div>
                    </div>
                </div>
            </section>
            {/* -- elements area end -- */}
        </>
    );
};

export default ElementsArea;