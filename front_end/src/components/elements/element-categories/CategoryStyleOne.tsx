import elementCategoriesData from '@/data/elements/element-categories';
import Link from 'next/link';
import React from 'react';

const CategoryStyleOne = () => {
    return (
        <>
            {/* -- category style 01 start -- */}
            <section className="bd-elements-category section-space">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Categories Style 01</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {elementCategoriesData.slice(0, 8).map((category) => (
                            <div className="col-xl-3 col-lg-4 col-md-6 col-sm-6" key={category.id}>
                                <Link href="/courses" className="bd-category-wrapper style-one wow bdFadeInUp" data-wow-delay=".4s">
                                    <div className="bd-category-item">
                                        <span className="bd-category-icon">
                                             {category.icon && React.createElement(category.icon)}
                                        </span>
                                        <div className="bd-category-content">
                                            <h6 className="bd-category-title">{category.title}</h6>
                                            <span className="bd-category-total">{category.totalCourses} course</span>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
            {/* -- category style 01 end -- */}
        </>
    );
};

export default CategoryStyleOne;