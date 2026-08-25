"use client"
import Link from 'next/link';
import React, { useState } from 'react';
import CourseGridCard from './CourseGridCard';
import CourseListCard from '../../common/courses-card/CourseListCard';
import coursesData from '@/data/courses/courses-data';
import AdBoxCard from '../../common/courses-card/AdBoxCard';
import { courseOrderEnum } from '@/data/dropdown-data';
import { courseCategoriesData } from '@/data/categories';
import { courseLevelsData } from '@/data/courses/LevelCheckbox';
import { courseSidebarPriceFilterData } from '@/data/courses/coursePriceFilterData';
import { courseRatingFilterData } from '@/data/courses/courseRatingFilterData';
import { Instructor, PriceFilter } from '@/interFace/interFace';
import { instructorsData, instructorsFilterDataTwo } from '@/data/courses/instructorFilterData';
import Breadcrumbs from '../../common/Breadcrumb/Breadcrumbs';
import { languagesData } from '@/data/courses/languageData';
import { videoDurationsData } from '@/data/courses/video-duration-data';
import { subcategoriesDataTwo } from '@/data/courses/subcategoryFilerData';
import { featuresDataTwo } from '@/data/courses/feature-filter-data';
import CourseFilterTwo from '@/components/common/course-filtering/CourseFilterTwo';
import NiceSelect from '@/components/elements/nice-select/NiceSelect';

const CoursesMain = () => {
    const [isGridView, setIsGridView] = useState(true);
    const [instructors, setInstructors] = useState<Instructor[]>(instructorsFilterDataTwo.slice(0, 3));
    const [ratingFilters, setRatingFilters] = useState(courseRatingFilterData);
    const [levels, setLevels] = useState(courseLevelsData);
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
    const handleLevelChange = (updatedLevels: typeof courseLevelsData) => {
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

    //handle toggle
    const handleGridClick = () => {
        setIsGridView(true);
    };
    const handleListClick = () => {
        setIsGridView(false);
    };
    const selectHandler = () => { }
    
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Advanced Course Filter' />
            {/* -- course area start -- */}
            <section className="bd-course-area section-space">
                <div className="container">
                    <div className="row g-30 align-items-center justify-content-between mb-30">
                        <div className="course-top-meta d-flex-between flex-wrap-small mb-30 gap-30">
                            <div className="bd-top-sorting-left">
                                <h6 className="bd-sorting-item-found">We found <span>14</span> courses available for you</h6>
                            </div>
                            <div className="bd-top-sorting-right d-flex flex-wrap-small align-items-center gap-15">
                                <div className="bd-layout-switcher">
                                    <label className={`bd-filter-type-text bd-grid-filter-text ${isGridView ? "active" : ""}`}>
                                        Grid
                                    </label>
                                    <label className={`bd-filter-type-text bd-list-filter-text ${!isGridView ? "active" : ""}`}>
                                        List
                                    </label>
                                    <ul className="bd-switcher-btn">
                                        <li>
                                            <button
                                                onClick={handleGridClick}
                                                className={`bd-filter-layout-trigger bd-grid-filter-trigger ${isGridView ? "active" : ""}`}
                                            >
                                                <i className="fa-solid fa-grid"></i>
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                onClick={handleListClick}
                                                className={`bd-filter-layout-trigger bd-list-filter-trigger ${!isGridView ? "active" : ""}`}
                                            >
                                                <i className="fa-solid fa-list"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div className="bd-sorting-select">
                                    <NiceSelect
                                        options={courseOrderEnum}
                                        filterIcon={true}
                                        defaultCurrent={0}
                                        onChange={selectHandler}
                                        name=""
                                        className="course-orderby"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        <div className="col-xxl-9 col-xl-9 col-lg-8">
                            {/* -- course grid style -- */}
                            <div className={`display-layout-grid ${isGridView ? "active" : ""}`} style={{ height: isGridView ? "auto" : "0", overflow: "hidden" }}>
                                <div className="row g-30">
                                    <CourseGridCard />
                                </div>
                            </div>
                            {/* -- course grid style end -- */}

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
                            {/* -- course-more style -- */}
                            <div className="bd-course-more-btn d-flex justify-content-center mt-50">
                                <Link className="bd-btn btn-outline-border-primary" href="#">Load More <span className="right-icon"><i
                                    className="fa-duotone fa-spinner"></i></span></Link>
                            </div>
                            {/* -- course-more style end -- */}
                        </div>
                        <div className="col-xxl-3 col-xl-3 col-lg-4">
                            <div className="bd-course-sidebar sidebar-right sidebar-sticky">
                                <CourseFilterTwo
                                    levels={levels}
                                    isPriceFilters={isPriceFilters}
                                    ratingFilters={ratingFilters}
                                    instructors={instructors}
                                    courseCategoriesData={courseCategoriesData}
                                    videoDurationsData={videoDurationsData.slice(2, 5)}
                                    subcategoriesData={subcategoriesDataTwo}
                                    languagesData={languagesData.slice(0, 3)}
                                    featuresData={featuresDataTwo}
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
            </section >
            {/* -- course area end -- */}
        </>
    );
};

export default CoursesMain;