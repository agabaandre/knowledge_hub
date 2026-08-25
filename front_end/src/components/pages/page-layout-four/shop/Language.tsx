import React from 'react';

const Language = () => {
    return (
        <>
            {/* -- Language Widget -- */}
            <div className="bd-shop-widget widget-language">
                <h5 className="bd-widget-title mb-20">Language</h5>
                <div className="bd-widget-content">
                    <div className="checkbox-option">
                        <input id="book-language-1" type="checkbox" />
                        <label htmlFor="book-language-1">English <span>(120)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-language-2" type="checkbox" />
                        <label htmlFor="book-language-2">Spanish <span>(30)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-language-3" type="checkbox" />
                        <label htmlFor="book-language-3">French <span>(15)</span></label>
                    </div>
                    <div className="checkbox-option">
                        <input id="book-language-4" type="checkbox" />
                        <label htmlFor="book-language-4">German <span>(10)</span></label>
                    </div>
                </div>
            </div>
            {/* -- Features Widget -- */}
        </>
    );
};

export default Language;