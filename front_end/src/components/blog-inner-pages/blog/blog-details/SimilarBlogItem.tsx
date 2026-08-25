import React from 'react';
//swiper functionality
import { Swiper, SwiperSlide, } from 'swiper/react';
import { Navigation, Pagination } from 'swiper/modules';
import BlogSingleCard from '@/components/common/blog/BlogSingleCard';
import blogData from '@/data/blog-data';

const SimilarBlogItem = () => {
    return (
        <>
            <div className="similarArticlesActivation">
                <Swiper
                    modules={[Navigation, Pagination]}
                    spaceBetween={30}
                    centeredSlides={false}
                    loop={true}
                    allowTouchMove={true}
                    observer={true}
                    className='swiper swiper-shadow-add'
                    navigation={{
                        nextEl: ".blog-navigation-next",
                        prevEl: ".blog-navigation-prev",
                    }}
                    breakpoints={{
                        1400: {
                            slidesPerView: 3,
                        },
                        1200: {
                            slidesPerView: 3,
                        },
                        992: {
                            slidesPerView: 2,
                        },
                        768: {
                            slidesPerView: 2,
                        },
                        576: {
                            slidesPerView: 1,
                        },
                        0: {
                            slidesPerView: 1,
                        },
                    }}
                >
                    {
                        blogData.slice(30, 36).map((item) => (
                            <SwiperSlide key={item.id}>
                                <BlogSingleCard item={item} />
                            </SwiperSlide>
                        ))
                    }
                </Swiper>
            </div>
        </>
    );
};

export default SimilarBlogItem;