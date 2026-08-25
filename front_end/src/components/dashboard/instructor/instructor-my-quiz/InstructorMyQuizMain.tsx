import { QuizAttempt } from '@/interFace/dashboard-interface';
import Link from 'next/link';
import React from 'react';

const quizAttempts: QuizAttempt[] = [
    { id: '#01', user: 'Chamain Louis', quiz: 'Introduction to Web Development', time: '15 Minutes', status: 'Complete' },
    { id: '#02', user: 'Chamain Louis', quiz: 'Advanced JavaScript Techniques', time: '20 Minutes', status: 'Incomplete' },
    { id: '#03', user: 'Chamain Louis', quiz: 'Data Structures and Algorithms', time: '25 Minutes', status: 'Complete' },
    { id: '#04', user: 'Chamain Louis', quiz: 'Introduction to Machine Learning', time: '30 Minutes', status: 'Pending' },
    { id: '#05', user: 'Chamain Louis', quiz: 'Introduction to Cloud Computing', time: '20 Minutes', status: 'Done' },
];

const InstructorMyQuizMain: React.FC = () => {
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
                                    <th>User</th>
                                    <th>Quiz</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {quizAttempts.map((attempt) => (
                                    <tr key={attempt.id}>
                                        <td><p>{attempt.id}</p></td>
                                        <td><p>{attempt.user}</p></td>
                                        <td><p>{attempt.quiz}</p></td>
                                        <td><p>{attempt.time}</p></td>
                                        <td>
                                            <div className={`bd-badge badge-${getStatusBadge(attempt.status)}`}>{attempt.status}</div>
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

// Helper function to determine the badge class based on status
const getStatusBadge = (status: 'Complete' | 'Incomplete' | 'Pending' | 'Done') => {
    switch (status) {
        case 'Complete': return 'primary';
        case 'Incomplete': return 'warning';
        case 'Pending': return 'danger';
        case 'Done': return 'success';
        default: return '';
    }
};

export default InstructorMyQuizMain;
