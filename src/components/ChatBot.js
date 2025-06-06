import { useState } from 'react';
import { Box, TextField, Button, List, ListItem, ListItemText, Typography } from '@mui/material';

export default function ChatBot() {
  const [messages, setMessages] = useState([]);
  const [text, setText] = useState('');
  const send = async () => {
    if (!text.trim()) return;
    const userText = text;
    setMessages([...messages, { from: 'user', text: userText }]);
    setText('');
    try {
      const res = await fetch('/wp-json/chatbot/v1/query', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ query: userText })
      });
      const data = await res.json();
      const answer = data.answer || data.message || '回答が得られませんでした';
      setMessages(msgs => [...msgs, { from: 'bot', text: answer }]);
    } catch (e) {
      setMessages(msgs => [...msgs, { from: 'bot', text: '通信エラーが発生しました' }]);
    }
  };
  return (
    <Box sx={{ maxWidth: 400, mx: 'auto', mt: 4 }}>
      <Typography variant="h6" sx={{ mb: 2 }}>ChatBot</Typography>
      <List sx={{ minHeight: 200, border: '1px solid #ccc', mb: 2 }}>
        {messages.map((m, i) => (
          <ListItem key={i}>
            <ListItemText primary={m.text} secondary={m.from === 'user' ? 'You' : 'Bot'} />
          </ListItem>
        ))}
      </List>
      <Box sx={{ display: 'flex' }}>
        <TextField value={text} onChange={e => setText(e.target.value)} fullWidth size="small" />
        <Button onClick={send} variant="contained" sx={{ ml: 1 }}>送信</Button>
      </Box>
    </Box>
  );
}

