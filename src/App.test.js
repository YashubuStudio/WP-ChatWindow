import { render, screen } from '@testing-library/react';
import App from './App';

test('renders chatbot heading', () => {
  render(<App />);
  const heading = screen.getByText(/ChatBot/i);
  expect(heading).toBeInTheDocument();
});

