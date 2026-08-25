import React from 'react';

const ShopAuthor = () => {
    return (
        <>
            {/* -- Author Widget -- */}
            <div className="bd-shop-widget widget-instructors">
                <h5 className="bd-widget-title mb-20">Authors</h5>
                <div className="bd-widget-content">
                    <div className="checkbox-option">
                        <input id="book-author-1" type="checkbox" defaultChecked />
                        <label htmlFor="book-author-1">J.K. Rowling <span>(8)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-author-2" type="checkbox" />
                        <label htmlFor="book-author-2">Stephen King <span>(12)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-author-3" type="checkbox" />
                        <label htmlFor="book-author-3">Agatha Christie <span>(6)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-author-4" type="checkbox" />
                        <label htmlFor="book-author-4">George R.R. Martin <span>(4)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-author-5" type="checkbox" />
                        <label htmlFor="book-author-5">J.R.R. Tolkien <span>(3)</span></label>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ShopAuthor;