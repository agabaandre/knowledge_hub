"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import AdBoxCard from '@/components/common/courses-card/AdBoxCard';
import CommonCourseSingleCardTwo from '@/components/common/courses-card/CommonCourseSingleCardTwo';
import CourseListCard from '@/components/common/courses-card/CourseListCard';
import NiceSelect from '@/components/elements/nice-select/NiceSelect';
import BasicPagination from '@/components/elements/pagination/BasicPagination';
import coursesData from '@/data/courses/courses-data';
import { courseOrderEnum } from '@/data/dropdown-data';
import React, { useState } from 'react';

const CourseListThreeMain = () => {
    const [isGridView, setIsGridView] = useState(false);

    const handleGridClick = () => {
        setIsGridView(true);
    };
    const handleListClick = () => {
        setIsGridView(false);
    };
    const selectHandler = () => { }

    return (
        <>
            <Breadcrumbs breadcrumbTitle='Course List No Sidebar' />
            {/* -- course list area start -- */}
            <section className="bd-course-list-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xxl-10 col-xl-10 col-lg-12 col-md-10">
                            <div className="course-top-meta d-flex-between flex-wrap mb-30 gap-30">
                                <div className="bd-top-sorting-left">
                                    <h6 className="bd-sorting-item-found">We found <span>14</span> courses available for you</h6>
                                </div>
                                <div className="bd-top-sorting-right d-flex align-items-center gap-15">
                                    <div className="bd-layout-switcher">
                                        <label className={`bd-filter-type-text bd-list-filter-text ${!isGridView ? "active" : ""}`}>
                                            List
                                        </label>
                                        <label className={`bd-filter-type-text bd-grid-filter-text ${isGridView ? "active" : ""}`}>
                                            Grid
                                        </label>
                                        <ul className="bd-switcher-btn">
                                            <li>
                                                <button onClick={handleListClick} className={`bd-filter-layout-trigger bd-list-filter-trigger ${!isGridView ? "active" : ""}`}>
                                                    <i className="fa-solid fa-list"></i>
                                                </button>
                                            </li>
                                            <li>
                                                <button onClick={handleGridClick} className={`bd-filter-layout-trigger bd-grid-filter-trigger ${isGridView ? "active" : ""}`}>
                                                    <i className="fa-solid fa-grid"></i>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div className="bd-sorting-select">
                                        <NiceSelect
                                            options={courseOrderEnum}
                                            defaultCurrent={0}
                                            onChange={selectHandler}
                                            filterIcon={true}
                                            name=""
                                            className="course-orderby"
                                        />
                                    </div>
                                </div>
                            </div>
                            {/* -- course list style -- */}
                            <div className={`display-layout-list ${!isGridView ? "active" : ""}`} style={{ height: !isGridView ? "auto" : "0", overflow: "hidden" }}>
                                <div className="bd-course-list">
                                    {
                                        coursesData.slice(22, 32).map((item) => (
                                            item.type === "course" ? (
                                                <CourseListCard key={item.id} course={item} />
                                            ) : (
                                                <AdBoxCard key={item.id} adbox={item} />
                                            )
                                        ))
                                    }
                                </div>
                            </div>
                            {/* -- course list style end -- */}
                            {/* -- course grid style -- */}
                            <div className={`display-layout-grid ${isGridView ? "active" : ""}`} style={{ height: isGridView ? "auto" : "0", overflow: "hidden" }}>
                                <div className="row g-30">
                                    {
                                        coursesData.slice(74, 83).map((course) => (
                                            <div className="col-xl-4" key={course.id}>
                                                <CommonCourseSingleCardTwo course={course} />
                                            </div>
                                        ))
                                    }
                                </div>
                            </div>
                            {/* -- course grid style end -- */}
                            {/* -- pagination style -- */}
                            <BasicPagination />
                            {/* -- pagination style end -- */}

                        </div>
                    </div>
                </div>
            </section>
            {/* -- course list area end -- */}
        </>
    );
};

export default CourseListThreeMain;