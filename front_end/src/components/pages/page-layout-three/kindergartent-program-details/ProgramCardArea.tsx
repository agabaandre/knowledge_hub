import React from "react";

const programCards = [
  { icon: "icon-humanities", title: "2-2.5 years", subtitle: "age", category: "cat-1" },
  { icon: "icon-cleander", title: "5 Days", subtitle: "weekly", category: "cat-2" },
  { icon: "icon-physical-education", title: "3.30 Hrs", subtitle: "period", category: "cat-3" },
  { icon: "icon-online-class", title: "Class Size", subtitle: "24", category: "cat-4" },
];

const ProgramCardArea = () => {
  return (
    <section className="bd-program-details-card-area section-space-medium-bottom">
      <div className="container">
        <div className="row gy-30 justify-content-center">
          {programCards.map((card, index) => (
            <div className="col-lg-3 col-md-6 col-sm-6" key={index}>
              <div className="bd-program-details-card-wrap mb-30">
                <div className="bd-program-details-card">
                  <div className="bd-program-details-card-content">
                    <div className={`bd-program-details-card-title ${card.category}`}>
                      <div className="bd-program-details-card-icon">
                        <i className={card.icon}></i>
                      </div>
                    </div>
                    <h6>{card.title}</h6>
                    <span>{card.subtitle}</span>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default ProgramCardArea;
