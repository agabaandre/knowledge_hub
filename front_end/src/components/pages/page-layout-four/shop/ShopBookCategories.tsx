import React from 'react';

const ShopBookCategories = () => {
    return (
        <>
            {/* -- Categories Widget -- */}
            <div className="bd-shop-widget widget-category">
                <h5 className="bd-widget-title mb-20">Book Categories</h5>
                <div className="bd-widget-content">
                    <div className="checkbox-option">
                        <input id="book-check-1" type="checkbox" defaultChecked />
                        <label htmlFor="book-check-1">Fiction <span>(120)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-check-2" type="checkbox" />
                        <label htmlFor="book-check-2">Non-Fiction <span>(85)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-check-3" type="checkbox" />
                        <label htmlFor="book-check-3">Mystery & Thriller <span>(45)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-check-4" type="checkbox" />
                        <label htmlFor="book-check-4">Romance <span>(30)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-check-5" type="checkbox" />
                        <label htmlFor="book-check-5">Science Fiction <span>(20)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-check-6" type="checkbox" />
                        <label htmlFor="book-check-6">Biographies <span>(25)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-check-7" type="checkbox" />
                        <label htmlFor="book-check-7">Fantasy <span>(35)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-check-8" type="checkbox" />
                        <label htmlFor="book-check-8">{`Children's`} Books <span>(18)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-check-9" type="checkbox" />
                        <label htmlFor="book-check-9">Self-Help <span>(14)</span></label>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ShopBookCategories;