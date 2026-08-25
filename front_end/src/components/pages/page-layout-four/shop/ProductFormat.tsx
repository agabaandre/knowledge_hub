import React from 'react';

const ProductFormat = () => {
    return (
        <>
            {/* -- Format Widget -- */}
            <div className="bd-shop-widget widget-level">
                <h5 className="bd-widget-title mb-20">Format</h5>
                <div className="bd-widget-content">
                    <div className="checkbox-option">
                        <input id="book-format-1" type="checkbox" />
                        <label htmlFor="book-format-1">Hardcover <span>(45)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-format-2" type="checkbox" />
                        <label htmlFor="book-format-2">Paperback <span>(75)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-format-3" type="checkbox" />
                        <label htmlFor="book-format-3">Ebook <span>(50)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-format-4" type="checkbox" />
                        <label htmlFor="book-format-4">Audiobook <span>(20)</span></label>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ProductFormat;