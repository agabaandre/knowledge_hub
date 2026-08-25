import ShopSingleCard from '@/components/common/ShopSingleCard';
import productsData from '@/data/products-data';
import React from 'react';

const StudentBooksMain = () => {
    return (
        <>
                <div className="col-xl-9 col-lg-9 col-md-8">
                    <div className="bd-dashboard-inner">
                        <div className="dashboard-enrolled-courses">
                            <div className="bd-dashboard-title-inner">
                                <h4 className="bd-dashboard-title">Student Books</h4>
                            </div>
                            <div className="container">
                                <div className="row g-30">
                                    {
                                        productsData.slice(13, 19).map((item) => (
                                            <div className="col-xl-4 col-lg-6" key={item.id}>
                                                <ShopSingleCard item={item} />
                                            </div>
                                        ))
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </>
    );
};

export default StudentBooksMain;