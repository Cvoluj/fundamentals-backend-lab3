<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../crud/subscriber.php';
require_once __DIR__ . '/../../crud/newsletter.php';

$allowed   = ['name','email','address'];
$sort_by   = in_array($_GET['sort_by'] ?? '', $allowed) ? $_GET['sort_by'] : 'id';
$order_in  = strtolower($_GET['order'] ?? 'asc');
$order     = $order_in === 'desc' ? 'DESC' : 'ASC';
$toggle    = $order === 'ASC' ? 'desc' : 'asc';

$sql = "
  SELECT id, name, email, address
    FROM subscribers
   ORDER BY {$sort_by} {$order}
";
$all = fetch_all_subscribers($sql);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>List of Subscribers</title>
  <link rel="stylesheet" href="/lab3/css/style.css">

</head>
<body>
  <h2>List of Subscribers</h2>

  <div style="margin-bottom:20px;">
    <a href="/lab3/subscribers/add">Add Subscriber</a> |
    <a href="/lab3/subscribers?sort_by=name&order=<?= $toggle ?>">
      Sort by Name <?= $order === 'ASC' ? '↑' : '↓' ?>
    </a> |
    <a href="/lab3/subscribers?sort_by=email&order=<?= $toggle ?>">
      Sort by Email <?= $order === 'ASC' ? '↑' : '↓' ?>
    </a> |
    <a href="/lab3/subscribers?sort_by=address&order=<?= $toggle ?>">
      Sort by Address <?= $order === 'ASC' ? '↑' : '↓' ?>
    </a>
  </div>

  <table border="1" cellpadding="5" cellspacing="0">
    <thead>
      <tr>
        <th>
          <a href="/lab3/subscribers?sort_by=id&order=<?= $toggle ?>">
            ID <?= $order === 'ASC' ? '↑' : '↓' ?>
          </a>
        </th>
        <th>Name</th>
        <th>Email</th>
        <th>Address</th>
        <th>Subscriptions</th>
        <th>Actions</th>
        <th>Subscribe</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($all as $row): 
      $id = (int)$row['id'];
    ?>
      <tr data-subscriber-id="<?= $id ?>">
        <td><?= htmlspecialchars($id) ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['address']) ?></td>
        <td class="subscriptions">
          <?php
            $subs = get_subscriptions($id);
            if ($subs) {
              foreach ($subs as $sub) {
                $nid     = (int)$sub['id'];
                $subject = htmlspecialchars($sub['subject']);
                $time    = htmlspecialchars($sub['subscribed_at']);
                echo "<div data-newsletter-id='{$nid}'>";
                echo    "<a href='/lab3/newsletters?id={$nid}'>$subject</a>";
                echo    " ({$time}) ";
                echo    "<button
                          type='button'
                          class='unsubscribe-button'
                          data-subscriber-id='{$id}'
                          data-newsletter-id='{$nid}'>
                          Unsubscribe
                        </button>";
                echo "</div>";
              }
            } else {
              echo '–';
            }
          ?>
        </td>
        <td>
          <a href="/lab3/subscribers/edit/<?= $id ?>">Edit</a> |
          <a href="/lab3/subscribers/delete/<?= $id ?>"
             onclick="return confirm('Delete subscriber #<?= $id ?>?')">
            Delete
          </a>
        </td>
        <td>
          <form class="subscribe-form"
                data-subscriber-id="<?= $id ?>"
                style="margin:0;display:inline">
            <select name="newsletter_id" required>
              <?php foreach (fetch_all_newsletters("SELECT id, subject FROM newsletters") as $n): ?>
                <option value="<?= (int)$n['id'] ?>">
                  <?= htmlspecialchars($n['subject']) ?>
                </option>
              <?php endforeach ?>
            </select>
            <button type="button" class="subscribe-button">
              Subscribe
            </button>
          </form>
        </td>
      </tr>
    <?php endforeach ?>
    </tbody>
  </table>

  <p><a href="/lab3/">Back to Main</a></p>

  <script>
    // Subscribe helper
    async function subscribe(subscriberId, newsletterId) {
      const fm = new FormData();
      fm.set('subscriber_id', subscriberId);
      fm.set('newsletter_id', newsletterId);
      const res = await fetch(
        '/lab3/views/subscribers/subscribe.php',
        { method:'POST', body: fm }
      );
      return res.json();
    }

    // Unsubscribe helper
    async function unsubscribe(subscriberId, newsletterId) {
      const fm = new FormData();
      fm.set('subscriber_id', subscriberId);
      fm.set('newsletter_id', newsletterId);
      const res = await fetch(
        '/lab3/views/subscribers/unsubscribe.php',
        { method:'POST', body: fm }
      );
      return res.json();
    }

    // Handle Subscribe
    document.querySelectorAll('.subscribe-button').forEach(btn => {
      btn.addEventListener('click', async () => {
        const form = btn.closest('.subscribe-form');
        const sid  = form.dataset.subscriberId;
        const nid  = form.newsletter_id.value;
        const result = await subscribe(sid, nid);

        if (result.status === 'ok') {
          const row  = document.querySelector(`tr[data-subscriber-id='${sid}']`);
          const cell = row.querySelector('.subscriptions');
          if (cell.textContent.trim() === '–') {
            cell.textContent = '';
          }

          const now      = new Date().toISOString().slice(0,19).replace('T',' ');
          const linkText = form.newsletter_id.selectedOptions[0].text;
          const div      = document.createElement('div');
          div.dataset.newsletterId = nid;

          const a = document.createElement('a');
          a.href = `/lab3/newsletters?id=${nid}`;
          a.textContent = linkText;

          const unsubBtn = document.createElement('button');
          unsubBtn.type = 'button';
          unsubBtn.className = 'unsubscribe-button';
          unsubBtn.dataset.subscriberId = sid;
          unsubBtn.dataset.newsletterId = nid;
          unsubBtn.textContent = 'Unsubscribe';
          unsubBtn.addEventListener('click', async () => {
            if (!confirm('Are you sure you want to unsubscribe?')) return;
            const resp = await unsubscribe(sid, nid);
            if (resp.status === 'ok') {
              div.remove();
              if (!cell.querySelector('div')) {
                cell.textContent = '–';
              }
            } else {
              alert('Error: ' + resp.message);
            }
          });

          div.append(a, ` (${now}) `, unsubBtn);
          cell.append(div);
        } else {
          alert('Error: ' + result.message);
        }
      });
    });

    // Handle existing Unsubscribe
    document.querySelectorAll('.unsubscribe-button').forEach(btn => {
      btn.addEventListener('click', async () => {
        if (!confirm('Are you sure you want to unsubscribe?')) return;
        const sid = btn.dataset.subscriberId;
        const nid = btn.dataset.newsletterId;
        const result = await unsubscribe(sid, nid);
        if (result.status === 'ok') {
          btn.closest('div[data-newsletter-id]').remove();
          const cell = document.querySelector(`tr[data-subscriber-id='${sid}'] .subscriptions`);
          if (!cell.querySelector('div')) {
            cell.textContent = '–';
          }
        } else {
          alert('Error: ' + result.message);
        }
      });
    });
  </script>
</body>
</html>
