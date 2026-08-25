import curriculumData from "@/data/program/curriculum-data";

const CurriculumArea: React.FC = () => {
  return (
    <div className="bd-program-curriculum mb-30">
      <h3 className="bd-course-details-content-title">Curriculum</h3>
      <span className="bd-course-curriculum-overview">66 courses | 160 credits</span>
      <div className="accordion-common-style accordion-transparent accordion-style-one">
        <div className="accordion" id="accordionExampleTwo">
          {curriculumData.map((yearData, yearIndex) => (
            <div className="accordion-item" key={yearIndex}>
              <h2 className="accordion-header" id={`heading${yearIndex}`}>
                <button 
                  className={`accordion-button ${yearIndex === 0 ? '' : 'collapsed'}`} 
                  type="button" 
                  data-bs-toggle="collapse" 
                  data-bs-target={`#collapse${yearIndex}`} 
                  aria-expanded={yearIndex === 0 ? "true" : "false"} 
                  aria-controls={`collapse${yearIndex}`}
                >
                  {yearData.year}
                </button>
              </h2>
              <div 
                id={`collapse${yearIndex}`} 
                className={`accordion-collapse collapse ${yearIndex === 0 ? 'show' : ''}`} 
                aria-labelledby={`heading${yearIndex}`} 
                data-bs-parent="#accordionExampleTwo"
              >
                <div className="accordion-body">
                  {yearData.semesters.map((semester, semIndex) => (
                    <div className="bd-program-curriculum-table table-responsive" key={semIndex}>
                      <h6 className="title">{semester.title}</h6>
                      <table className="table table-bordered text-center">
                        <thead className="table-light">
                          <tr>
                            <th scope="col">Sl.</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Title</th>
                            <th scope="col" colSpan={2}>Theory</th>
                            <th scope="col" colSpan={2}>Sessional</th>
                            <th scope="col">Total Credits</th>
                          </tr>
                          <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Contact hrs/week</th>
                            <th>Credit</th>
                            <th>Contact hrs/week</th>
                            <th>Credit</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          {semester.courses.map((course, courseIndex) => (
                            <tr key={courseIndex}>
                              <td>{course.sl}</td>
                              <td>{course.code}</td>
                              <td>{course.title}</td>
                              <td>{course.theoryHours !== null ? course.theoryHours.toFixed(2) : "--"}</td>
                              <td>{course.theoryCredit !== null ? course.theoryCredit.toFixed(2) : "--"}</td>
                              <td>{course.sessionalHours !== null ? course.sessionalHours.toFixed(2) : "--"}</td>
                              <td>{course.sessionalCredit !== null ? course.sessionalCredit.toFixed(2) : "--"}</td>
                              <td>{course.totalCredit.toFixed(2)}</td>
                            </tr>
                          ))}
                        </tbody>
                        <tfoot>
                          <tr className="table-light">
                            <th colSpan={3}>Total</th>
                            <td>{semester.total.theoryHours.toFixed(2)}</td>
                            <td>{semester.total.theoryCredit.toFixed(2)}</td>
                            <td>{semester.total.sessionalHours.toFixed(2)}</td>
                            <td>{semester.total.sessionalCredit.toFixed(2)}</td>
                            <td>{semester.total.totalCredit.toFixed(2)}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default CurriculumArea;

