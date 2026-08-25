import React from 'react';

const ProductFeatures = () => {
    return (
        <>
            {/* -- Features Widget -- */}
            <div className="bd-shop-widget widget-features">
                <h5 className="bd-widget-title mb-20">Features</h5>
                <div className="bd-widget-content">
                    <div className="checkbox-option">
                        <input id="book-features-1" type="checkbox" />
                        <label htmlFor="book-features-1">Best Sellers <span>(40)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-features-2" type="checkbox" />
                        <label htmlFor="book-features-2">New Releases <span>(25)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-features-3" type="checkbox" />
                        <label htmlFor="book-features-3">Award Winners <span>(10)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-features-4" type="checkbox" />
                        <label htmlFor="book-features-4">Highly Rated <span>(30)</span></label>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ProductFeatures;