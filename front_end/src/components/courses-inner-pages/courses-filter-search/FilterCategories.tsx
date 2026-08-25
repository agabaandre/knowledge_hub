import React from 'react';

const FilterCategories = () => {
    return (
        <>
            <div className="bd-widget-content">
                <div className="checkbox-option">
                    <input id="course-check-1" type="checkbox" />
                    <label htmlFor="course-check-1">Data Science <span>(20)</span></label>
                </div>
                <div className="checkbox-option">
                    <input id="course-check-2" type="checkbox" />
                    <label htmlFor="course-check-2">Machine Learning <span>(18)</span></label>
                </div>
                <div className="checkbox-option">
                    <input id="course-check-3" type="checkbox" />
                    <label htmlFor="course-check-3">Computer Science <span>(25)</span></label>
                </div>
                <div className="checkbox-option">
                    <input id="course-check-4" type="checkbox" />
                    <label htmlFor="course-check-4">Business Administration <span>(12)</span></label>
                </div>
                <div className="checkbox-option">
                    <input id="course-check-5" type="checkbox" />
                    <label htmlFor="course-check-5">Finance <span>(10)</span></label>
                </div>
                <div className="checkbox-option">
                    <input id="course-check-6" type="checkbox" />
                    <label htmlFor="course-check-6">Marketing <span>(14)</span></label>
                </div>
                <div className="checkbox-option">
                    <input id="course-check-7" type="checkbox" />
                    <label htmlFor="course-check-7">Graphic Design <span>(9)</span></label>
                </div>
                <div className="checkbox-option">
                    <input id="course-check-8" type="checkbox" />
                    <label htmlFor="course-check-8">Psychology <span>(11)</span></label>
                </div>
                <div className="checkbox-option">
                    <input id="course-check-9" type="checkbox" />
                    <label htmlFor="course-check-9">Engineering <span>(16)</span></label>
                </div>
                <div className="checkbox-option">
                    <input id="course-check-10" type="checkbox" />
                    <label htmlFor="course-check-10">Language Learning <span>(13)</span></label>
                </div>
            </div>
        </>
    );
};

export default FilterCategories;