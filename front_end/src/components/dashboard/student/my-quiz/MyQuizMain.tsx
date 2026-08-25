import Link from 'next/link';
import React from 'react';

const quizData = [
    {
        id: '#01',
        user: 'Chamain Louis',
        quiz: 'Introduction to Web Development',
        time: '15 Minutes',
        status: 'Complete',
        badgeClass: 'badge-primary',
    },
    {
        id: '#02',
        user: 'Chamain Louis',
        quiz: 'Advanced JavaScript Techniques',
        time: '20 Minutes',
        status: 'Incomplete',
        badgeClass: 'badge-warning',
    },
    {
        id: '#03',
        user: 'Chamain Louis',
        quiz: 'Data Structures and Algorithms',
        time: '25 Minutes',
        status: 'Complete',
        badgeClass: 'badge-primary',
    },
    {
        id: '#04',
        user: 'Chamain Louis',
        quiz: 'Introduction to Machine Learning',
        time: '30 Minutes',
        status: 'Pending',
        badgeClass: 'badge-danger',
    },
    {
        id: '#05',
        user: 'Chamain Louis',
        quiz: 'Introduction to Cloud Computing',
        time: '20 Minutes',
        status: 'Done',
        badgeClass: 'badge-success',
    },
];

const MyQuizMain = () => {
    return (
            <div className="col-xl-9 col-lg-9 col-md-8">
                <div className="bd-dashboard-inner">
                    <div className="bd-dashboard-title-inner">
                        <h4 className="bd-dashboard-title">My Quiz Attempts</h4>
                    </div>
                    <div className="bd-dashboard-table table-responsive mt-30">
                        <table className="table table-bordered table-head-bg">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th style={{ minWidth: '150px' }}>User</th>
                                    <th style={{ minWidth: '308px' }}>Quiz</th>
                                    <th style={{ minWidth: '119px' }}>Time</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {quizData.map((quiz) => (
                                    <tr key={quiz.id}>
                                        <td>
                                            <p>{quiz.id}</p>
                                        </td>
                                        <td>
                                            <p>{quiz.user}</p>
                                        </td>
                                        <td>
                                            <p>{quiz.quiz}</p>
                                        </td>
                                        <td>
                                            <p>{quiz.time}</p>
                                        </td>
                                        <td>
                                            <div className={`bd-badge ${quiz.badgeClass}`}>{quiz.status}</div>
                                        </td>
                                        <td>
                                            <div className="bd-button-action">
                                                <Link className="bd-default-tooltip edit" href="#">
                                                    <span><i className="fa-light fa-pen-to-square"></i></span>
                                                </Link>
                                                <Link className="bd-default-tooltip view" href="#">
                                                    <span><i className="fa-sharp fa-light fa-eye"></i></span>
                                                </Link>
                                                <Link className="bd-default-tooltip delete" href="#">
                                                    <span><i className="fa-light fa-trash-can"></i></span>
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    );
};

export default MyQuizMain;
