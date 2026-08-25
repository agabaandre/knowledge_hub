import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import categoryArtDesign from '../../../../public/assets/images/category/design.webp';
import categoryWeb from '../../../../public/assets/images/category/web-dev.webp';
import categoryDataScience from '../../../../public/assets/images/category/data-science.webp';
import categoryMarketing from '../../../../public/assets/images/category/marketing.webp';
import categoryPhotography from '../../../../public/assets/images/category/photography.webp';
import categoryProgramming from '../../../../public/assets/images/category/programming.webp';
import categoryFinance from '../../../../public/assets/images/category/finance.webp';
import categoryHealth from '../../../../public/assets/images/category/health.webp';

const CategoryStyleSeven = () => {
    const categories = [
        { imgSrc: categoryArtDesign, title: "Arts & Design", coursesCount: 5 },
        { imgSrc: categoryWeb, title: "Web Development", coursesCount: 12 },
        { imgSrc: categoryDataScience, title: "Data Science", coursesCount: 8 },
        { imgSrc: categoryMarketing, title: "Digital Marketing", coursesCount: 10 },
        { imgSrc: categoryPhotography, title: "Photography", coursesCount: 7 },
        { imgSrc: categoryProgramming, title: "Programming", coursesCount: 15 },
        { imgSrc: categoryFinance, title: "Finance & Accounting", coursesCount: 9 },
        { imgSrc: categoryHealth, title: "Health & Wellness", coursesCount: 11 }
    ];

    return (
        <section className="bd-elements-category section-space-bottom">
            <div className="container">
                <div className="row align-items-center justify-content-center">
                    <div className="col-lg-12">
                        <div className="bd-elements-section-wrapper section-title-space text-center">
                            <div className="bd-elements-line">
                                <div className="bd-separator-line line-left"></div>
                                <div className="bd-elements-title-wrapper">
                                    <h2 className="bd-elements-title small">Categories Style 07</h2>
                                </div>
                                <div className="bd-separator-line line-right"></div>
                            </div>
                            <p className=""></p>
                        </div>
                    </div>
                </div>
                <div className="row g-30">
                    {categories.map((category, index) => (
                        <div className="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-6 col-xxs-12" key={index}>
                            <div className="bd-category-wrapper style-two">
                                <div className="bd-category-item">
                                    <div className="bd-category-thumb-box">
                                        <div className="bd-category-thumb">
                                            <Link href="#"><Image src={category.imgSrc} alt="category" /></Link>
                                        </div>
                                    </div>
                                    <div className="bd-category-content">
                                        <h6 className="bd-category-title underline">
                                            <Link href="#">{category.title}</Link>
                                        </h6>
                                        <span className="bd-category-subtitle">Course: <span className="total-course">{category.coursesCount}</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default CategoryStyleSeven;
