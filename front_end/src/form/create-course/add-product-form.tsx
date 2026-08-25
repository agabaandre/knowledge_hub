"use client"
import NiceSelect from '@/components/elements/nice-select/NiceSelect';
import { courseLavel } from '@/data/dropdown-data';
import React from 'react';

const AddProductForm = () => {

    const selectHandler = () => { }
    return (
        <>
            <div className="bd-course-product">
                <div className="bd-course-product-item">
                    <div className="bd-course-product-left">
                        <p className="title b3">Course Type</p>
                    </div>
                    <div className="bd-course-product-right">
                        <div className="radio d-flex-items gap-30">
                            <div className="form-check">
                                <input name="default-radio-1" className="form-check-input" type="radio" defaultValue="" id="defaultRadio1" defaultChecked/>
                                <label className="form-check-label" htmlFor="defaultRadio1">
                                    Paid
                                </label>
                            </div>
                            <div className="form-check">
                                <input name="default-radio-1" className="form-check-input" type="radio" defaultValue="" id="defaultRadio2" />
                                <label className="form-check-label" htmlFor="defaultRadio2">
                                    Free
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="bd-course-product-item">
                    <div className="bd-course-product-left">
                        <p className="title b3">Select product</p>
                    </div>
                    <div className="bd-course-product-right">
                        <div className="bd-new-course-input-level">
                            <NiceSelect
                                options={courseLavel}
                                defaultCurrent={0}
                                onChange={selectHandler}
                                filterIcon={false}
                                name=""
                                className="course-name"
                            />
                        </div>
                    </div>
                </div>
                <div className="bd-course-product-item">
                    <div className="bd-course-product-left">
                        <p className="title b3">Regular Price</p>
                    </div>
                    <div className="bd-course-product-right">
                        <div className="form-input">
                            <input name="regularPrice" type="number" />
                        </div>
                    </div>
                </div>
                <div className="bd-course-product-item">
                    <div className="bd-course-product-left">
                        <p className="title b3">Sale Price (Discounted Price)</p>
                    </div>
                    <div className="bd-course-product-right">
                        <div className="form-input">
                            <input name="salePrice" type="number" />
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default AddProductForm;