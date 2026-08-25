"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import CourseFilter from '@/components/common/course-filtering/CourseFilter';
import { courseCategoriesDataTwoThree } from '@/data/categories';
import { courseSidebarPriceFilterData } from '@/data/courses/coursePriceFilterData';
import { courseRatingFilterDataTwo } from '@/data/courses/courseRatingFilterData';
import { featuresData } from '@/data/courses/feature-filter-data';
import { instructorsData, instructorsFilterDataTwo } from '@/data/courses/instructorFilterData';
import { sidebarCourseFilterLanguagesData } from '@/data/courses/languageData';
import { sidebarCourseLevelsFilterData } from '@/data/courses/LevelCheckbox';
import { subcategoriesData } from '@/data/courses/subcategoryFilerData';
import { videoDurationsData } from '@/data/courses/video-duration-data';
import { IInstructorFilter, PriceFilter } from '@/interFace/interFace';
import Link from 'next/link';
import React, { useState } from 'react';
import CourseFilterTwo from '@/components/common/course-filtering/CourseFilterTwo';
import coursesData from '@/data/courses/courses-data';
import CommonCourseSingleCardTwo from '@/components/common/courses-card/CommonCourseSingleCardTwo';
import useGlobalContext from '@/hooks/useContexts';

const CourseGridLeftMain = () => {
    const { toggleOpen } = useGlobalContext();
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
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Course Grid Left' />
            {/* -- course list area start -- */}
            <section className="bd-course-list-area section-space">
                <div className="container">
                    <div className="row g-30 align-items-center justify-content-between mb-30">
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
                        <div className="col-xxl-3 col-xl-4 col-lg-3 order-lg-0 order-1">
                            <div className="bd-course-sidebar sidebar-left sidebar-sticky">
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
                        <div className="col-xxl-9 col-xl-8 col-lg-9 order-lg-1 order-0">
                            <div className="row g-30">
                                {
                                    coursesData.slice(74, 83).map((course) => (
                                        <div className="col-xl-6 col-lg-6 col-md-6" key={course.id}>
                                            <CommonCourseSingleCardTwo course={course} />
                                        </div>
                                    ))
                                }
                                {/* -- course-more style -- */}
                                <div className="bd-course-more-btn d-flex justify-content-center mt-50">
                                    <Link className="bd-btn btn-outline-border-primary" href="#">Load More <span className="right-icon"><i
                                        className="fa-duotone fa-spinner"></i></span></Link>
                                </div>
                                {/* -- course-more style end -- */}

                            </div>
                        </div>
                    </div>
                    {/* -- course grid style end -- */}
                </div>
            </section>
            {/* -- course list area end -- */}
        </>
    );
};

export default CourseGridLeftMain;