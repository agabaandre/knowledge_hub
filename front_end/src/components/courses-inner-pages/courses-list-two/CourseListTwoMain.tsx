"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import BasicPagination from '@/components/elements/pagination/BasicPagination';
import { courseCategoriesDataTwoThree } from '@/data/categories';
import { courseSidebarPriceFilterData } from '@/data/courses/coursePriceFilterData';
import { courseRatingFilterDataTwo } from '@/data/courses/courseRatingFilterData';
import { featuresData } from '@/data/courses/feature-filter-data';
import { instructorsData, instructorsFilterDataTwo } from '@/data/courses/instructorFilterData';
import { sidebarCourseFilterLanguagesData } from '@/data/courses/languageData';
import { sidebarCourseLevelsFilterData } from '@/data/courses/LevelCheckbox';
import { subcategoriesData } from '@/data/courses/subcategoryFilerData';
import { videoDurationsData } from '@/data/courses/video-duration-data';
import { courseOrderEnum } from '@/data/dropdown-data';
import { IInstructorFilter, PriceFilter } from '@/interFace/interFace';
import React, { useState } from 'react';
import CourseFilterTwo from '@/components/common/course-filtering/CourseFilterTwo';
import CommonCourseSingleCardTwo from '@/components/common/courses-card/CommonCourseSingleCardTwo';
import coursesData from '@/data/courses/courses-data';
import CourseListCard from '../../common/courses-card/CourseListCard';
import AdBoxCard from '../../common/courses-card/AdBoxCard';
import NiceSelect from '@/components/elements/nice-select/NiceSelect';

const CourseListTwoMain = () => {
    const [isGridView, setIsGridView] = useState(false);
    const [instructors, setInstructors] = useState<IInstructorFilter[]>(instructorsFilterDataTwo.slice(0, 5));
    const [ratingFilters, setRatingFilters] = useState(courseRatingFilterDataTwo);
    const [levels, setLevels] = useState(sidebarCourseLevelsFilterData);
    const [isPriceFilters, setIsPriceFilters] = useState(courseSidebarPriceFilterData);
    const [, setSelectedFeatures] = useState<string[]>([]);
    const [, setSelectedDurations] = useState<string[]>([]);

    //rating handle filter
    const handleFilterChange = (stars: number, isChecked: boolean) => {
        setRatingFilters((prevFilters) => prevFilters.map((filter) => filter.stars === stars ? { ...filter, isChecked } : filter))
    };

    // Function to handle price checkbox state change
    const handlePriceFilterChange = (updatedFilters: PriceFilter[]) => {
        setIsPriceFilters(updatedFilters);
    };

    // Function to handle level checkbox state change
    const handleLevelChange = (updatedLevels: typeof sidebarCourseLevelsFilterData) => {
        setLevels(updatedLevels);
    };
    // Function to handle instructor checkbox state change
    const instructorFilterChange = (updatedInstructors: typeof instructorsData) => {
        setInstructors(updatedInstructors);
    };

    const handleFeatureChange = (id: string, isChecked: boolean) => {
        setSelectedFeatures((prev) =>
            isChecked ? [...prev, id] : prev.filter((featureId) => featureId !== id)
        );
    };
    //hangle duration change
    const handleDurationChange = (id: string, isChecked: boolean) => {
        setSelectedDurations((prev) =>
            isChecked ? [...prev, id] : prev.filter((durationId) => durationId !== id)
        );
    };
    const handleGridClick = () => {
        setIsGridView(true);
    };
    const handleListClick = () => {
        setIsGridView(false);
    };
    const selectHandler = () => { }

    return (
        <>
            <Breadcrumbs breadcrumbTitle='Course List Right' />
            {/* -- course list area start -- */}
            <section className="bd-course-list-area section-space">
                <div className="container">
                    <div className="row">
                        <div className="col-xxl-9 col-xl-9 col-lg-8">
                            <div className="course-top-meta d-flex-between flex-wrap mb-30 gap-30">
                                <div className="bd-top-sorting-left">
                                    <h6 className="bd-sorting-item-found">We found <span>14</span> courses available for you</h6>
                                </div>
                                <div className="bd-top-sorting-right d-flex flex-wrap-small align-items-center gap-15">
                                    <div className="bd-layout-switcher">
                                        <label className={`bd-filter-type-text bd-list-filter-text ${!isGridView ? "active" : ""}`}>
                                            List
                                        </label>
                                        <label className={`bd-filter-type-text bd-grid-filter-text ${isGridView ? "active" : ""}`}>
                                            Grid
                                        </label>
                                        <ul className="bd-switcher-btn">
                                            <li>
                                                <button
                                                    onClick={handleListClick}
                                                    className={`bd-filter-layout-trigger bd-list-filter-trigger ${!isGridView ? "active" : ""}`}
                                                >
                                                    <i className="fa-solid fa-list"></i>
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    onClick={handleGridClick}
                                                    className={`bd-filter-layout-trigger bd-grid-filter-trigger ${isGridView ? "active" : ""}`}
                                                >
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
                                <div className="bd-course-list with-sidebar">
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
                                            <div className="col-xl-6 col-lg-12 col-md-12" key={course.id}>
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
                        <div className="col-xxl-3 col-xl-3 col-lg-4">
                            <div className="bd-course-sidebar sidebar-right sidebar-sticky">
                                <CourseFilterTwo
                                    levels={levels}
                                    isPriceFilters={isPriceFilters}
                                    ratingFilters={ratingFilters}
                                    instructors={instructors}
                                    courseCategoriesData={courseCategoriesDataTwoThree}
                                    videoDurationsData={videoDurationsData}
                                    subcategoriesData={subcategoriesData}
                                    languagesData={sidebarCourseFilterLanguagesData}
                                    featuresData={featuresData}
                                    handleLevelChange={handleLevelChange}
                                    handlePriceFilterChange={handlePriceFilterChange}
                                    handleFilterChange={handleFilterChange}
                                    instructorFilterChange={instructorFilterChange}
                                    handleDurationChange={handleDurationChange}
                                    handleFeatureChange={handleFeatureChange}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- course list area end -- */}
        </>
    );
};

export default CourseListTwoMain;