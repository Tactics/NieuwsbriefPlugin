<?php use_helper('Object'); ?>

<h1>Bewerken</h1>
<p>
  U kan hieronder uw gegevens wijzigen.
</p>

<?php if (isset($opgeslagen)) :?>
<div style='border: 1px solid black; font-weight: bold;'>
Uw wijzigingen werden opgeslagen!
</div>
<?php endif ?>


<?php echo form_tag('ttNieuwsbrief/update');?>
  
  <?php echo object_input_hidden_tag($email_adres, 'getEmail'); ?>
  <?php echo object_input_hidden_tag($email_adres, 'getUuid'); ?>
  
  <fieldset style='padding-left: 10px;'>
    <legend style='font-size: 14px;'>Persoonlijke gegevens</legend>
    <table>
      <tr>
        <th>
          Uw emailadres:
        </th>
        <td>
          <?php echo $email_adres->getEmail(); ?>
        </td>
      </tr>
      <tr>
        <th>
          Voornaam:
        </th>
        <td>
          <?php
          if ($email_adres instanceof EmailAdres)
          {
            echo object_input_tag($email_adres, 'getVoornaam');
          }
          else {
            echo $email_adres->getVoornaam();
          }
          ?>
        </td>
      </tr>
      <tr>
        <th>
          Achternaam:
        </th>
        <td>
          <?php
          if ($email_adres instanceof EmailAdres)
          {
            echo object_input_tag($email_adres, 'getAchternaam');
          }
          else {
            echo $email_adres->getAchternaam();
          }
          ?>
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset style='padding-left: 10px;'>
    <legend style='font-size: 14px;'>Interesses</legend>

    <p>Duid aan over welke onderwerpen u informatie wenst te ontvangen. Indien u geen interesses aanduidt, ontvangt u ook geen email.</p>   
      <ul>   
    
      <?php foreach($interesse_gebieden as $gebied) : ?>
        <?php 
         $id = 'interesse_' . $gebied->getId();
        ?>
        <li>
          <?php echo checkbox_tag($id, 1, isset($persoonlijke_interesses[$gebied->getId()])); ?>
          <label for='<?php echo $id ?>' style='float:none'>
            <?php echo $gebied->getNaam(); ?>
          </label>
          <i>
            <?php echo $gebied->getOmschrijving(); ?>
          </i>
        </li>
      <?php endforeach ?>
      </ul>
  
  </fieldset>

<?php echo submit_tag('Sla de wijzigingen op'); ?>
</form>

<br /><br />

<h1>Uitschrijven</h1>
<?php echo form_tag('ttNieuwsbrief/uitschrijven');?>
  <?php echo object_input_hidden_tag($email_adres, 'getEmail'); ?>
  <?php echo object_input_hidden_tag($email_adres, 'getUuid'); ?>
  <p>
  <label style='float:none'>
    <?php echo checkbox_tag('bevestig_uitschrijven', 1, false);  ?>
    Ja, ik wens mij uit te schrijven van alle nieuwsbrieven.
  </label><br/><br />
  </p>
  <?php echo submit_tag('Schrijf mij uit'); ?>
</form>

