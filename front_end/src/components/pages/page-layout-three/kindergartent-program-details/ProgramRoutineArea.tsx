import React from 'react';

const ProgramRoutineArea = () => {
    return (
        <>
            {/* -- program routine area start here -- */}
            <section className="bd-routine-area section-space-medium-bottom">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-lg-8">
                            <div className="bd-section-title-wrapper text-center mb-55">
                                <h2 className="bd-section-title mb-0">Full Day with Learning</h2>
                                <p>With the help of teachers and the environment as the third teacher, students<br /> have
                                    opportunities to confidently take risks.</p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        <div className="col-lg-6">
                            <div className="bd-routine-table">
                                <table className="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Hour</th>
                                            <th scope="col">Activity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>8:00am</td>
                                            <td>Free Play</td>
                                        </tr>
                                        <tr>
                                            <td>8:30am</td>
                                            <td>Sand Pit</td>
                                        </tr>
                                        <tr>
                                            <td>9:00am</td>
                                            <td>Tattoo Corner</td>
                                        </tr>
                                        <tr>
                                            <td>9:30am</td>
                                            <td>Creativity Corner</td>
                                        </tr>
                                        <tr>
                                            <td>10:00am</td>
                                            <td>Food Time</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="bd-routine-table">
                                <table className="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Hour</th>
                                            <th scope="col">Activity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>10:30am</td>
                                            <td>Creativity Corner</td>
                                        </tr>
                                        <tr>
                                            <td>11:00am</td>
                                            <td>Food Time</td>
                                        </tr>
                                        <tr>
                                            <td>11:30am</td>
                                            <td>Free Play</td>
                                        </tr>
                                        <tr>
                                            <td>12:00am</td>
                                            <td>Tattoo Corner</td>
                                        </tr>
                                        <tr>
                                            <td>12:30am</td>
                                            <td>Sand Pit</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- program routine area end here  -- */}
        </>
    );
};

export default ProgramRoutineArea;