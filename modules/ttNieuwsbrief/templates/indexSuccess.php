<h1>Inschrijven</h1>
<p>
  Indien u op de hoogte wenst te blijven van onze activiteiten, vul dan hieronder uw gegevens in.  
</p>

<?php if (isset($reeds_lid)) :?>
<div style='border: 1px solid black; font-weight: bold;'>
Het emailadres dat u opgaf is reeds in ons systeem opgenomen!  Indien u uw voorkeuren wenst aan te passen, gebruik dan het andere formulier.
</div>
<?php endif ?>

<?php if (isset($ongeldig_adres)) :?>
<div style='border: 1px solid black; font-weight: bold;'>
Gelieve een geldig emailadres op te geven!
</div>
<?php endif ?>


<?php echo form_tag('ttNieuwsbrief/inschrijven');?>

  <fieldset style='padding-left: 10px;'>
    <legend style='font-size: 14px;'>Persoonlijke gegevens</legend>
    <table>
      <tr>
        <th>
          Emailadres:
        </th>
        <td>
          <?php echo input_tag('email', $sf_params->get('Email')) ?>
        </td>
      </tr>
      <tr>
        <th>
          Voornaam:
        </th>
        <td>
          <?php echo input_tag('voornaam', $sf_params->get('Voornaam')) ?>
        </td>
      </tr>
      <tr>
        <th>
          Achternaam:
        </th>
        <td>
          <?php echo input_tag('achternaam', $sf_params->get('Achternaam')) ?>
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
          <br />
          <i>
            <?php echo $gebied->getOmschrijving(); ?>
          </i>
          </li>
      <?php endforeach ?>
      </ul>
  
  </fieldset>

<p>
  Na inschrijven ontvangt u een mail met een link, u moet uw inschrijving via deze link bevestigen voor u emails kan ontvangen.
</p>

<?php echo submit_tag('Schrijf mij in !'); ?>
  
</form>

<br />
<br />

<h1>Uitschrijven of voorkeuren aanpassen</h1>
<p>
  Om u uit te schrijven van onze mailinglijsten of uw voorkeuren aan te passen, geef u emailadres hieronder in.
  U ontvangt dan een email met een beveiligde link naar de locatie waar u deze aanpassingen kan doen.
</p>

<?php echo form_tag('ttNieuwsbrief/sendLink');?>  

  <fieldset style='padding-left: 10px;'>
    <legend style='font-size: 14px;'>Persoonlijke gegevens</legend>
    <table>
      <tr>
        <th>Uw emailadres:</th>
        <td><?php echo input_tag('email', $sf_params->get('Email')) ?></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td>
          <?php echo submit_tag('Stuur me de beveiligde link'); ?>
        </td>
      </tr>
    </table>
  </fieldset>
</form>
