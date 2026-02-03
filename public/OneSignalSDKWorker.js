importScripts('https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.sw.js');
// self.addEventListener('notificationclick', function(event) {
//   if (!event.action) {
//     // المستخدم ضغط على الإشعار نفسه
//     event.waitUntil(clients.openWindow('https://example.com/default'));
//     return;
//   }
//   switch(event.action) {
//     case 'accept_button':
//       event.waitUntil(clients.openWindow('https://example.com/accept'));
//       break;
//     case 'decline_button':
//       event.waitUntil(clients.openWindow('https://example.com/decline'));
//       break;
//     default:
//       event.waitUntil(clients.openWindow('https://example.com'));
//       break;
//   }
// });
