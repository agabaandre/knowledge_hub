import counterData from '@/data/counter-data';
import React from 'react';
import CountUpContent from '../common/counter/CountUpContent';

const ModernSchoolingCounter = () => {
    return (
        <>
            {/* -- counter area start -- */}
            <div className="bd-counter-area theme-bg section-space-small fix">
                <div className="container">
                    <div className="row gy-30">
                        {
                            counterData.slice(4, 8).map((item) => (
                                <div className="col-lg-3 col-md-6 col-sm-6" key={item.id}>
                                    <div className="bd-counter-item bd-counter-style-nine">
                                        <h2 className="bd-counter-total">
                                            <CountUpContent number={item.counterNum} text={item.suffix}/>
                                        </h2>
                                        <p>{item.counterText}</p>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </div>
            {/* -- counter area end -- */}
        </>
    );
};

export default ModernSchoolingCounter;