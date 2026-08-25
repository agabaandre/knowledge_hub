import Image from 'next/image';
import { timelineData } from '@/data/history-data';

const HistoryCenturies = () => {
  return (
    <>
      <section className="bd-history-centuries section-space-bottom">
        <div className="container">
          <div className="row">
            <div className="bd-history-timeline">
              {timelineData.map((timeline) => (
                <div className="bd-history-timeline-single" id={timeline.id} key={timeline.id}>
                  <div className="bd-history-timeline-thumb">
                    <Image src={timeline.image} style={{ width: '100%', height: 'auto' }} alt="image" />
                  </div>
                  <div className="bd-history-timeline-content">
                    <h4 className="title">{timeline.title}</h4>
                    <p className="bd-history-timeline-desc">{timeline.description}</p>
                    <ul>
                      {timeline.events.map((event, index) => (
                        <li key={index}>
                          <span>{event.year}:</span> {event.description}
                        </li>
                      ))}
                    </ul>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>
    </>
  );
};

export default HistoryCenturies;