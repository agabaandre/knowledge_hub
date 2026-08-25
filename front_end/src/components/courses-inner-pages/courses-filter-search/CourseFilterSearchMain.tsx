"use client"
import { useState } from 'react';
import CommonCourseSingleCard from '../../common/courses-card/CommonCourseSingleCard';
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import coursesData from '@/data/courses/courses-data';
import CourseListCard from '../../common/courses-card/CourseListCard';
import AdBoxCard from '../../common/courses-card/AdBoxCard';
import CourseFilter from '@/components/common/course-filtering/CourseFilter';
import useGlobalContext from '@/hooks/useContexts';

const CourseFilterSearchMain = () => {
    const { toggleOpen } = useGlobalContext();
    const [isGridView, setIsGridView] = useState(true);

    const handleGridClick = () => {
        setIsGridView(true);
    };

    const handleListClick = () => {
        setIsGridView(false);
    };

    return (
        <>
            <Breadcrumbs breadcrumbTitle='Courses Search Filter' />
            {/* -- course list area start -- */}
            <section className="bd-course-list-area section-space">
                <div className="container">
                    <div className="row gy-30 align-items-center justify-content-between mb-30">
                        <div className="col-xl-5 col-lg-5 col-md-12 col-12">
                            <div className="d-flex-between">
                                <div className="bd-top-sorting-left">
                                    <h6 className="bd-sorting-item-found">We found <span>12</span> courses available for you</h6>
                                </div>
                                <div className="bd-layout-switcher">
                                    <label className={`bd-filter-type-text bd-grid-filter-text ${isGridView ? "active" : ""}`}>Grid</label>
                                    <label className={`bd-filter-type-text bd-list-filter-text ${!isGridView ? "active" : ""}`}>List</label>
                                    <ul className="bd-switcher-btn">
                                        <li>
                                            <button onClick={handleGridClick} className={`bd-filter-layout-trigger bd-grid-filter-trigger ${isGridView ? "active" : ""}`}>
                                                <i className="fa-solid fa-grid"></i>
                                            </button>
                                        </li>
                                        <li>
                                            <button onClick={handleListClick} className={`bd-filter-layout-trigger bd-list-filter-trigger ${!isGridView ? "active" : ""}`}>
                                                <i className="fa-solid fa-list"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-7 col-md-12 col-12">
                            <div className="d-flex-between gap-30">
                                <div className="bd-course-filter-search text-center w-100">
                                    <form className="bd-course-filter-search-form" action="#" method="get">
                                        <input type="text" defaultValue="" name="s" placeholder="Search" />
                                        <button type="submit"> <i className="far fa-search"></i> </button>
                                    </form>
                                </div>
                                <div className="bd-filter-btn">
                                    <button onClick={toggleOpen} className="bd-btn btn-outline-primary">
                                        Filter <span className="right-icon"><i className="fa-regular fa-filter"></i></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <CourseFilter
                        onFilterChange={() => { }}
                        sliceLimits={{
                            instructors: 5,
                            levels: 4,
                            priceFilters: 5,
                            categories: 10,
                            languages: 6,
                        }}
                    />
                    {/* -- course grid style -- */}
                    <div className={`display-layout-grid ${isGridView ? "active" : ""}`} style={{ height: isGridView ? "auto" : "0", overflow: "hidden" }}>
                        <div className="row gy-30">
                            {
                                coursesData.slice(32, 41).map((item) => (
                                    <div className="col-xl-4 col-lg-6 col-md-6" key={item.id}>
                                        <CommonCourseSingleCard course={item} />
                                    </div>
                                ))
                            }
                        </div>
                    </div>
                    {/* -- course grid style end -- */}

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
                </div>
            </section>
            {/* -- course list area end -- */}
        </>
    );
};

export default CourseFilterSearchMain;