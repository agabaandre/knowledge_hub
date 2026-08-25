import React from 'react';
import storyImg1 from '../../../../../public/assets/images/story/story-1.webp';
import storyImg2 from '../../../../../public/assets/images/story/story-2.webp';
import storyImg3 from '../../../../../public/assets/images/story/story-3.webp';
import Image from 'next/image';

const QuranLearningStoryArea = () => {
    return (
        <>
            {/* -- our story area start -- */}
            <section className="bd-story-area section-space-bottom">
                <div className="container">
                    <div className="row gy-30 justify-content-between align-items-end">
                        <div className="col-xl-6 col-lg-6">
                            <div className="bd-story-thumb"><Image style={{ width: "100%", height: "auto" }} src={storyImg1} alt="story" /></div>
                        </div>
                        <div className="col-xl-6 col-lg-6">
                            <div className="bd-story-content">
                                <div className="bd-section-title-wrapper section-title-space">
                                    <h2 className="bd-section-title mb-20">Our Story</h2>
                                    <p className="bd-section-paragraph">Lorem ipsum, dolor sit amet consectetur adipisicing
                                        elit. Voluptatibus magni rerum tenetur. Expedita placeat enim.</p>
                                    <p className="bd-section-paragraph">Lorem ipsum, dolor sit amet consectetur adipisicing
                                        elit. Voluptatibus magni rerum tenetur. Expedita placeat enim doloribus deleniti
                                        nobis
                                        blanditiis cupiditate quo aperiam repudiandae! Est, vel.</p>
                                </div>

                                <div className="row gy-30">
                                    <div className="col-lg-6 col-md-6">
                                        <div className="bd-story-thumb"><Image style={{ width: "100%", height: "auto" }} src={storyImg2} alt="story" />
                                        </div>
                                    </div>
                                    <div className="col-lg-6 col-md-6">
                                        <div className="bd-story-thumb"><Image style={{ width: "100%", height: "auto" }} src={storyImg3} alt="story" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- our story area end -- */}
        </>
    );
};

export default QuranLearningStoryArea;