"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import CourseFilter from '@/components/common/course-filtering/CourseFilter';
import coursesData from '@/data/courses/courses-data';
import React from 'react';
import CourseGridThreeCard from '../../common/courses-card/CourseGridThreeCard';
import useGlobalContext from '@/hooks/useContexts';

const CourseGridThreeMain = () => {
    const { toggleOpen } = useGlobalContext();
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Course Grid Style 03' />
            {/* -- course grid area start -- */}
            <section className="bd-course-grid-area section-space">
                <div className="container">
                    <div className="row gy-30 align-items-center justify-content-between mb-30">
                        <div className="col-xl-5 col-lg-5 col-md-12 col-12">
                            <div className="d-flex-between">
                                <div className="bd-top-sorting-left">
                                    <h6 className="bd-sorting-item-found">We found <span>12</span> courses available for you</h6>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-2 col-lg-7 col-md-12 col-12">
                            <div className="bd-filter-btn text-lg-end">
                                <button onClick={toggleOpen} className="bd-btn btn-outline-primary">
                                    Filter <span className="right-icon"><i className="fa-regular fa-filter"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    {/* Course filter */}
                    <CourseFilter
                        onFilterChange={(filters) => console.log(filters)}
                        sliceLimits={{
                            instructors: 5,
                            levels: 4,
                            priceFilters: 5,
                            categories: 10,
                            languages: 6,
                        }}
                    />

                    {/* -- course grid style -- */}
                    <div className="row gy-30">
                        {
                            coursesData.slice(50, 59).map((item, index) => (
                                <div className='col-xl-4 col-lg-6 col-md-6' key={index}>
                                    <CourseGridThreeCard course={item}/>
                                </div>

                            ))
                        }
                    </div>
                    {/* -- course grid style end -- */}

                    {/* -- course-more style -- */}
                    <div className="bd-course-more-btn d-flex justify-content-center mt-50">
                        <a className="bd-btn btn-outline-border-primary" href="#">Load More <span className="right-icon"><i
                            className="fa-duotone fa-spinner"></i></span></a>
                    </div>
                    {/* -- course-more style end -- */}
                </div>
            </section>
            {/* -- course grid area end -- */}
        </>
    );
};

export default CourseGridThreeMain;