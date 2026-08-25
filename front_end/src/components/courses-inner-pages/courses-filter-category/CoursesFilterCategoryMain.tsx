"use client"
import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import Breadcrumbs from '../../common/Breadcrumb/Breadcrumbs';
import coursesData from '@/data/courses/courses-data';
import CommonCourseSingleCard from '../../common/courses-card/CommonCourseSingleCard';

const CoursesFilterCategoryMain = () => {
    const [filteredCategory, setFilteredCategory] = useState('All');

    // Filter courses based on the selected category
    const filteredCourses = filteredCategory === 'All'
        ? coursesData.slice(32, 41)
        : coursesData.slice(32, 41).filter(course => course?.category?.includes(filteredCategory));


    // Smooth slide-up animation
    const containerVariants = {
        hidden: { opacity: 0, y: 50 },
        visible: {
            opacity: 1,
            y: 0,
            transition: { 
                duration: 0.5, 
                ease: [0.16, 1, 0.3, 1] as const, 
            },
        },
    };

    return (
        <>
            <Breadcrumbs breadcrumbTitle="Courses Filter Category" />
            <section className="bd-course-list-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xxl-12">
                            <div className="bd-filter-course course-item text-center mb-50">
                                {/* Category buttons */}
                                {['All', 'Business', 'Development', 'Finance', 'Lifestyle', 'Marketing', 'Programming', 'Recipe', 'Technology'].map(category => (
                                    <button
                                        key={category}
                                        className={`filter-item ${filteredCategory === category ? 'active' : ''}`}
                                        onClick={() => setFilteredCategory(category)}
                                    >
                                        {category}
                                    </button>
                                ))}
                            </div>
                            <div className="row g-30 grid">
                                {
                                    <AnimatePresence>
                                        {filteredCourses.map(course => (
                                            <motion.div
                                                variants={containerVariants}
                                                key={course.id}
                                                className='col-xl-4 col-lg-6 col-md-6'
                                                initial="hidden"
                                                animate="visible"
                                            >
                                                <CommonCourseSingleCard course={course} />
                                            </motion.div>
                                        ))}
                                    </AnimatePresence>
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default CoursesFilterCategoryMain;
