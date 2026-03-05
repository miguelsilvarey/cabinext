import nodemailer from 'nodemailer';

export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).send('error');

  const { firstName, lastName, email, message } = req.body;

  if (!firstName || !lastName || !message || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    return res.status(400).send('error');
  }

  const transporter = nodemailer.createTransport({
    service: 'gmail',
    auth: {
      user: process.env.miguelsilvarey@gmail.com,
      pass: process.env.ozdb mnmo npkt dltq, // contraseña de aplicación, no tu clave normal
    },
  });

  const safeMsg = message
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/\n/g, '<br>');

  try {
    await transporter.sendMail({
      from: `"CABINEXT Website" <${process.env.miguelsilvarey@gmail.com}>`,
      to: 'cabinextllc@gmail.com',
      replyTo: `${firstName} ${lastName} <${email}>`,
      subject: `New Contact Form Submission from ${firstName} ${lastName}`,
      html: `
        <h3>Website Contact</h3>
        <p><strong>Name:</strong> ${firstName} ${lastName}</p>
        <p><strong>Email:</strong> ${email}</p>
        <p><strong>Message:</strong><br>${safeMsg}</p>
      `,
      text: `Name: ${firstName} ${lastName}\nEmail: ${email}\n\nMessage:\n${message}`,
    });

    res.status(200).send('success');
  } catch (err) {
    console.error('Mailer error:', err);
    res.status(500).send('error');
  }
}
