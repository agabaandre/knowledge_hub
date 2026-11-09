import pino from 'pino';

export const createLogger = ({ logLevel, isProduction }) =>
  pino({
    level: logLevel,
    transport: isProduction ? undefined : { target: 'pino-pretty', options: { colorize: true } },
    redact: ['req.headers.authorization', 'req.headers.cookie'],
  });
