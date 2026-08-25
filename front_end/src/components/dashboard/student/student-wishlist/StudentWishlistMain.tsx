import CourseGridThreeCard from '@/components/common/courses-card/CourseGridThreeCard';
import coursesData from '@/data/courses/courses-data';
import React from 'react';

const StudentWishlistMain = () => {
    return (
        <>
            <div className="col-xl-9 col-lg-9 col-md-8">
                <div className="bd-dashboard-inner">
                    <div className="bd-dashboard-title-inner">
                        <h4 className="bd-dashboard-title">Student Wishlist</h4>
                    </div>
                    <div className="container">
                        <div className="row g-30">
                            {
                                coursesData.slice(54, 58).map((item, index) => (
                                    <div className='col-xl-6 col-lg-6 col-md-12' key={index}>
                                        <CourseGridThreeCard course={item} />
                                    </div>
                                ))
                            }
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default StudentWishlistMain;